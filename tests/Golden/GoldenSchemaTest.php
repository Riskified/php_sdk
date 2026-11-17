<?php

namespace Riskified\Tests\Golden;

use Opis\JsonSchema\Errors\ValidationError;
use Opis\JsonSchema\Validator;
use PHPUnit\Framework\TestCase;
use Riskified\Common\Env;
use Riskified\Common\Riskified;
use Riskified\Common\Validations;

/**
 * Validates every golden sample against the request-body schema of each endpoint it's sent to, taken
 * from the OpenAPI definitions in the API reference (schema/, refreshed by schema/update-schemas.sh).
 * Keys the docs don't list are allowed. Fields the docs type wrongly are corrected by SCHEMA_OVERRIDES.
 */
class GoldenSchemaTest extends TestCase {
    const SCHEMA_DIR = __DIR__ . '/schema';

    const ORDER_SCHEMAS = ['pre-auth-decide', 'post-auth-decide', 'post-auth-submit'];
    const CHECKOUT_DENIED_SCHEMAS = ['pre-auth-checkout_denied', 'post-auth-checkout_denied'];

    // Sample name => schema files (schema/<name>.json) for the endpoints it's sent to.
    // chargeback goes to /chargeback, which isn't part of the alignment.
    const SCHEMAS_BY_SAMPLE = [
        'card_order' => self::ORDER_SCHEMAS,
        'paypal_order' => self::ORDER_SCHEMAS,
        'bank_transfer_order' => self::ORDER_SCHEMAS,
        'digital_order' => self::ORDER_SCHEMAS,
        'travel_order' => self::ORDER_SCHEMAS,
        'ride_order' => self::ORDER_SCHEMAS,
        'event_order' => self::ORDER_SCHEMAS,
        'accommodation_order' => self::ORDER_SCHEMAS,
        'checkout_denied' => self::CHECKOUT_DENIED_SCHEMAS,
        'decision' => ['pre-auth-decision'],
        'order_1_14' => self::ORDER_SCHEMAS,
        'decision_1_14' => ['pre-auth-decision'],
    ];

    // Component.property => the definition the API actually takes, replacing the documented one before
    // validating, so the field is still checked. Delete an entry once the docs are fixed.
    const SCHEMA_OVERRIDES = [
        // docs: string (float)
        'seller.starting_price' => ['type' => 'number'],
        // docs: array of address
        'rides_line_item.pickup_address' => ['$ref' => '#/components/schemas/address'],
        'rides_line_item.dropoff_address' => ['$ref' => '#/components/schemas/address'],
        // docs: number (date-time); the server accepts a string too
        'recipient_ACH.created_at' => ['type' => 'string', 'format' => 'date-time'],
        'recipient_ACH.updated_at' => ['type' => 'string', 'format' => 'date-time'],
    ];

    /** @var array<string, mixed> */
    public array $riskifiedSnapshot = [];

    protected function setUp(): void {
        $this->riskifiedSnapshot = [
            'domain' => Riskified::$domain,
            'auth_token' => Riskified::$auth_token,
            'env' => Riskified::$env,
            'validations' => Riskified::$validations,
        ];
        Riskified::init('shop.example.com', 'token', Env::SANDBOX, Validations::IGNORE_MISSING);
    }

    protected function tearDown(): void {
        Riskified::$domain = $this->riskifiedSnapshot['domain'];
        Riskified::$auth_token = $this->riskifiedSnapshot['auth_token'];
        Riskified::$env = $this->riskifiedSnapshot['env'];
        Riskified::$validations = $this->riskifiedSnapshot['validations'];
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function sampleSchemaPairs(): array {
        $pairs = [];
        foreach (self::SCHEMAS_BY_SAMPLE as $sample => $schemas) {
            foreach ($schemas as $schema) {
                $pairs["$sample $schema"] = [$sample, $schema];
            }
        }
        return $pairs;
    }

    /**
     * @dataProvider sampleSchemaPairs
     */
    public function testSampleMatchesApiSchema(string $sample, string $schema): void {
        [$method, $model] = GoldenSamples::all()[$sample];
        $body = json_decode(CapturingTransport::capture($method, $model)->lastJson);

        $result = (new Validator(null, PHP_INT_MAX, false))->validate($body, self::loadRequestSchema($schema));
        $errors = $result->error() === null ? [] : array_values(array_unique(self::flatten($result->error())));
        sort($errors);

        $this->assertSame([], $errors, "Schema errors in $sample against $schema");
    }

    public function testEveryOverrideMatchesADocumentedProperty(): void {
        $unmatched = [];
        foreach (array_keys(self::SCHEMA_OVERRIDES) as $path) {
            [$component, $property] = explode('.', $path);
            $found = false;
            foreach (array_unique(array_merge(...array_values(self::SCHEMAS_BY_SAMPLE))) as $schema) {
                $definition = json_decode(file_get_contents(self::SCHEMA_DIR . "/$schema.json"));
                $found = $found || isset($definition->components->schemas->$component->properties->$property);
            }
            if (!$found) {
                $unmatched[] = $path;
            }
        }

        $this->assertSame([], $unmatched, 'Overrides with no matching property in the vendored schemas');
    }

    public function testEverySampleWithAnEndpointInScopeIsValidated(): void {
        $unvalidated = array_values(array_diff(array_keys(GoldenSamples::all()), array_keys(self::SCHEMAS_BY_SAMPLE)));

        $this->assertSame(['chargeback'], $unvalidated);
    }

    /**
     * The request-body schema becomes the root; components ride along so its #/components/schemas refs resolve.
     * SCHEMA_OVERRIDES replace the documented definitions they name.
     */
    public static function loadRequestSchema(string $schema): object {
        $definition = json_decode(file_get_contents(self::SCHEMA_DIR . "/$schema.json"));
        foreach (self::SCHEMA_OVERRIDES as $path => $override) {
            [$component, $property] = explode('.', $path);
            $properties = $definition->components->schemas->$component->properties ?? null;
            if (isset($properties->$property)) {
                $properties->$property = json_decode(json_encode($override));
            }
        }
        $operation = current((array) current((array) $definition->paths));

        $root = clone $operation->requestBody->content->{'application/json'}->schema;
        $root->components = $definition->components;
        return $root;
    }

    /**
     * Reduces the error tree to "<path> <keyword> [detail]" leaves. anyOf / oneOf failures carry one
     * error list per branch (e.g. one per line item type). Report only the branch the value matches
     * best, otherwise every line item would also list the required fields of every other line item
     * type. A value outside a branch's enum (product_type, payment_type) means it's the wrong branch,
     * so those rank first and the total error count breaks ties. allOf keeps every branch's errors.
     * @return string[]
     */
    public static function flatten(ValidationError $error): array {
        $subErrors = $error->subErrors();
        if ($subErrors === []) {
            return self::describe($error);
        }

        $branches = array_map([self::class, 'flatten'], $subErrors);
        if (!in_array($error->keyword(), ['anyOf', 'oneOf'], true)) {
            return array_merge(...$branches);
        }

        $rank = fn (array $branch) => [
            count(array_filter($branch, fn ($e) => str_ends_with($e, ' enum'))),
            count($branch),
        ];
        usort($branches, fn ($a, $b) => $rank($a) <=> $rank($b));
        return $branches[0];
    }

    /**
     * @return string[]
     */
    public static function describe(ValidationError $error): array {
        $path = implode('.', $error->data()->fullPath());
        $args = $error->args();
        switch ($error->keyword()) {
            case 'required':
                return array_map(fn ($property) => ltrim("$path.$property", '.') . ' required', $args['missing']);
            case 'type':
                return ["$path type " . implode('|', (array) $args['expected'])];
            case 'format':
                return ["$path format {$args['format']}"];
            default:
                return ["$path {$error->keyword()}"];
        }
    }
}
