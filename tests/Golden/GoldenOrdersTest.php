<?php

namespace Riskified\Tests\Golden;

use PHPUnit\Framework\TestCase;
use Riskified\Common\Env;
use Riskified\Common\Riskified;
use Riskified\Common\Validations;
use Riskified\OrderWebhook\Model\AbstractModel;

/**
 * Pins the exact payload each flow sends. Run with GOLDEN_UPDATE=1 to regenerate the snapshots on purpose.
 */
class GoldenOrdersTest extends TestCase {
    const SNAPSHOT_DIR = __DIR__ . '/snapshots';

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
     * @dataProvider samples
     */
    public function testPayloadMatchesSnapshot(string $name, string $method, AbstractModel $model): void {
        $transport = CapturingTransport::capture($method, $model);

        $this->assertNotNull($transport->lastJson, "$method did not send a request");
        $actual = [
            'endpoint' => $transport->lastEndpoint,
            'body' => json_decode($transport->lastJson, true),
        ];

        $path = self::SNAPSHOT_DIR . "/$name.json";
        if (getenv('GOLDEN_UPDATE') === '1') {
            file_put_contents($path, json_encode($actual, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n");
        }

        $this->assertFileExists($path, "Missing snapshot; run with GOLDEN_UPDATE=1 to create it");
        $expected = json_decode(file_get_contents($path), true);

        // assertSame catches type changes (e.g. "40.7" vs 40.7); sorting keys ignores field order.
        $this->assertSame(self::sortKeys($expected), self::sortKeys($actual));
    }

    /**
     * @return array<string, array{string, string, AbstractModel}>
     */
    public static function samples(): array {
        $cases = [];
        foreach (GoldenSamples::all() as $name => [$method, $model]) {
            $cases[$name] = [$name, $method, $model];
        }
        return $cases;
    }

    public static function sortKeys(mixed $value): mixed {
        if (!is_array($value)) {
            return $value;
        }
        $value = array_map([self::class, 'sortKeys'], $value);
        if (!array_is_list($value)) {
            ksort($value);
        }
        return $value;
    }
}
