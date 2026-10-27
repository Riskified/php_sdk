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
 * Keys the docs don't list are allowed. Known gaps are listed in knownGaps(); remove an entry when the
 * step that fixes it lands.
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

    const ORDERS = [
        'card_order', 'paypal_order', 'bank_transfer_order', 'digital_order',
        'travel_order', 'ride_order', 'event_order', 'accommodation_order', 'order_1_14',
    ];
    // Orders whose line item is the physical one, which carries vendor and seller.
    const PHYSICAL_ORDERS = ['card_order', 'paypal_order', 'bank_transfer_order', 'order_1_14'];

    /** @var array<string, mixed> */
    public array $riskifiedSnapshot = [];

    /**
     * Each entry names the plan step (API_ALIGNMENT_PLAN.md) or open question that resolves it.
     * @return string[]
     */
    public static function knownGaps(): array {
        return array_merge(
            self::gap(self::ORDERS, self::ORDER_SCHEMAS, 'order.billing_address type array', 'step 3.1'),
            self::gap(self::ORDERS, self::ORDER_SCHEMAS, 'order.shipping_address type array', 'step 3.1'),
            self::gap(['ride_order'], self::ORDER_SCHEMAS, 'order.line_items.0.pickup_address type array', 'step 3.2'),
            self::gap(['ride_order'], self::ORDER_SCHEMAS, 'order.line_items.0.dropoff_address type array', 'step 3.2'),
            self::gap(
                ['decision', 'decision_1_14'],
                ['pre-auth-decision'],
                'order.payment_details type array',
                'step 3.3'
            ),
            self::gap(
                self::ORDERS,
                self::ORDER_SCHEMAS,
                'order.charge_free_payment_details type array',
                'step 3.4'
            ),
            self::gap(
                ['checkout_denied'],
                ['post-auth-checkout_denied'],
                'checkout.charge_free_payment_details type array',
                'step 3.4'
            ),
            self::gap(['event_order'], self::ORDER_SCHEMAS, 'order.line_items.0.latitude type number', 'step 3.5'),
            self::gap(['event_order'], self::ORDER_SCHEMAS, 'order.line_items.0.longitude type number', 'step 3.5'),
            self::gap(
                ['accommodation_order'],
                self::ORDER_SCHEMAS,
                'order.line_items.0.rating type string',
                'step 3.5'
            ),
            self::gap(self::PHYSICAL_ORDERS, self::ORDER_SCHEMAS, 'order.line_items.0.vendor type object', 'step 3.6'),
            self::gap(
                ['checkout_denied'],
                ['post-auth-checkout_denied'],
                'checkout.line_items.0.vendor type object',
                'step 3.6'
            ),
            self::gap(
                ['checkout_denied'],
                self::CHECKOUT_DENIED_SCHEMAS,
                'checkout.payment_details.0.authorization_error required',
                'step 3.8'
            ),
            self::gap(
                self::PHYSICAL_ORDERS,
                self::ORDER_SCHEMAS,
                'order.line_items.0.seller.starting_price type string',
                'step 3.10 / question 7'
            ),
            self::gap(
                ['checkout_denied'],
                ['post-auth-checkout_denied'],
                'checkout.line_items.0.seller.starting_price type string',
                'step 3.10 / question 7'
            )
        );
    }

    /**
     * $fixedBy is documentation only; it isn't part of the matched string.
     * @param string[] $samples
     * @param string[] $schemas
     * @return string[]
     */
    public static function gap(array $samples, array $schemas, string $error, string $fixedBy): array {
        $gaps = [];
        foreach ($samples as $sample) {
            foreach ($schemas as $schema) {
                $gaps[] = "$sample $schema: $error";
            }
        }
        return $gaps;
    }

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
    public function testSampleMatchesApiSchemaExceptKnownGaps(string $sample, string $schema): void {
        [$method, $model] = GoldenSamples::all()[$sample];
        $body = json_decode(CapturingTransport::capture($method, $model)->lastJson);

        $result = (new Validator(null, PHP_INT_MAX, false))->validate($body, self::loadRequestSchema($schema));
        $actual = $result->error() === null ? [] : self::flatten($result->error());
        $actual = array_map(fn ($error) => "$sample $schema: $error", array_unique($actual));
        $expected = array_filter(self::knownGaps(), fn ($gap) => str_starts_with($gap, "$sample $schema: "));

        $new = array_diff($actual, $expected);
        $fixed = array_diff($expected, $actual);
        sort($new);
        sort($fixed);
        $this->assertTrue(
            $new === [] && $fixed === [],
            "New schema errors:\n  " . implode("\n  ", $new)
                . "\nKnown gaps that no longer occur (remove them from knownGaps()):\n  " . implode("\n  ", $fixed)
        );
    }

    public function testEverySampleWithAnEndpointInScopeIsValidated(): void {
        $unvalidated = array_values(array_diff(array_keys(GoldenSamples::all()), array_keys(self::SCHEMAS_BY_SAMPLE)));

        $this->assertSame(['chargeback'], $unvalidated);
    }

    /**
     * The request-body schema becomes the root; components ride along so its #/components/schemas refs resolve.
     */
    public static function loadRequestSchema(string $schema): object {
        $definition = json_decode(file_get_contents(self::SCHEMA_DIR . "/$schema.json"));
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
