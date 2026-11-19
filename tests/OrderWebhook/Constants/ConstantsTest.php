<?php

namespace Riskified\Tests\OrderWebhook\Constants;

use PHPUnit\Framework\TestCase;
use Riskified\OrderWebhook\Constants;
use Riskified\OrderWebhook\Model\Order;

class ConstantsTest extends TestCase {
    // Class => the property whose enum it mirrors, found in every vendored schema
    const PROPERTIES = [
        Constants\OrderSource::class => 'source',
        Constants\AiAgent::class => 'ai_agent',
        Constants\PaymentType::class => 'payment_type',
        Constants\AuthorizationType::class => 'authorization_type',
        Constants\AcquirerRegion::class => 'acquirer_region',
        Constants\ProductType::class => 'product_type',
        Constants\TransportMethod::class => 'transport_method',
        Constants\DeliveredTo::class => 'delivered_to',
        Constants\TransStatus::class => 'trans_status',
        Constants\KycType::class => 'kyc_type',
        Constants\SocialSignupType::class => 'social_signup_type',
    ];

    // Classes whose enum is a named component rather than a property
    const COMPONENTS = [
        Constants\ExternalStatus::class => 'decision_type',
        Constants\CryptoWalletType::class => 'crypto_wallet.type',
    ];

    /**
     * @return array<string, array{class-string, list<string>}>
     */
    public static function documentedValues(): array {
        $schemas = [];
        foreach (glob(__DIR__ . '/../../Golden/schema/*.json') as $file) {
            $schemas[] = json_decode(file_get_contents($file), true);
        }

        $cases = [];
        foreach (self::PROPERTIES as $class => $property) {
            $values = [];
            foreach ($schemas as $schema) {
                self::collectEnums($schema, $property, $values);
            }
            $cases[$class] = [$class, $values];
        }
        foreach (self::COMPONENTS as $class => $path) {
            $parts = explode('.', $path);
            $values = [];
            foreach ($schemas as $schema) {
                $node = $schema['components']['schemas'][$parts[0]] ?? null;
                if (isset($parts[1])) {
                    $node = $node['properties'][$parts[1]] ?? null;
                }
                $values = array_merge($values, $node['enum'] ?? []);
            }
            $cases[$class] = [$class, $values];
        }
        return $cases;
    }

    /**
     * Collects the enum of every schema property named $property, skipping examples.
     * @param mixed $node
     * @param list<string> $values
     */
    private static function collectEnums($node, string $property, array &$values): void {
        if (!is_array($node)) {
            return;
        }
        if (isset($node['properties'][$property]['enum'])) {
            $values = array_merge($values, $node['properties'][$property]['enum']);
        }
        foreach ($node as $key => $child) {
            if ($key !== 'example' && $key !== 'examples') {
                self::collectEnums($child, $property, $values);
            }
        }
    }

    /**
     * @dataProvider documentedValues
     * @param class-string $class
     * @param list<string> $documented
     */
    public function testConstantsMatchTheDocumentedValues(string $class, array $documented): void {
        $this->assertNotEmpty($documented, "no enum found in the schemas for $class");

        $constants = array_values((new \ReflectionClass($class))->getConstants());
        $documented = array_values(array_unique($documented));
        sort($constants);
        sort($documented);

        $this->assertSame($documented, $constants);
    }

    public function testEveryConstantsClassIsCompared(): void {
        $files = glob(__DIR__ . '/../../../src/Riskified/OrderWebhook/Constants/*.php');
        $classes = array_map(function ($file) {
            return 'Riskified\\OrderWebhook\\Constants\\' . basename($file, '.php');
        }, $files);
        $compared = array_merge(array_keys(self::PROPERTIES), array_keys(self::COMPONENTS));
        sort($classes);
        sort($compared);

        $this->assertSame($classes, $compared);
    }

    public function testOrderAcceptsEveryOrderSourceAndAiAgent(): void {
        foreach ((new \ReflectionClass(Constants\OrderSource::class))->getConstants() as $source) {
            (new Order(['source' => $source]))->validate(false);
        }
        foreach ((new \ReflectionClass(Constants\AiAgent::class))->getConstants() as $agent) {
            (new Order(['ai_agent' => $agent]))->validate(false);
        }
        $this->addToAssertionCount(1);
    }
}
