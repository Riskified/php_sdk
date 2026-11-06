<?php

namespace Riskified\Tests\OrderWebhook\Transport;

use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use Riskified\Common\Env;
use Riskified\Common\Riskified;
use Riskified\Common\Signature\HttpDataSignature;
use Riskified\Common\Validations;
use Riskified\OrderWebhook\Exception\MultiplePropertiesException;
use Riskified\OrderWebhook\Model\Login;
use Riskified\OrderWebhook\Model\Order;
use Riskified\OrderWebhook\Transport\CurlTransport;
use Riskified\Tests\Golden\CapturingTransport;

class AbstractTransportTest extends TestCase {
    private function readProtectedProperty(object $object, string $property): mixed {
        $ref = new ReflectionProperty($object, $property);
        $ref->setAccessible(true);

        return $ref->getValue($object);
    }

    /** @var array<string, mixed> */
    private array $riskifiedSnapshot = [];

    protected function setUp(): void {
        $this->riskifiedSnapshot = [
            'domain' => Riskified::$domain,
            'auth_token' => Riskified::$auth_token,
            'env' => Riskified::$env,
            'validations' => Riskified::$validations,
        ];
    }

    protected function tearDown(): void {
        Riskified::$domain = $this->riskifiedSnapshot['domain'];
        Riskified::$auth_token = $this->riskifiedSnapshot['auth_token'];
        Riskified::$env = $this->riskifiedSnapshot['env'];
        Riskified::$validations = $this->riskifiedSnapshot['validations'];
    }

    public function testConstructionSetsUserAgent(): void {
        Riskified::init('shop', 'token', Env::SANDBOX, Validations::IGNORE_MISSING);
        $transport = new CurlTransport(new HttpDataSignature());

        $this->assertSame(
            'riskified_php_sdk/' . Riskified::VERSION,
            $this->readProtectedProperty($transport, 'user_agent')
        );
    }

    public function testConstructionSetsUrlFromRiskifiedGetHost(): void {
        Riskified::init('shop', 'token', Env::SANDBOX, Validations::IGNORE_MISSING);
        $transport = new CurlTransport(new HttpDataSignature());

        $this->assertSame(Riskified::getHost(), $this->readProtectedProperty($transport, 'url'));
    }

    public function testConstructionAcceptsCustomUrl(): void {
        Riskified::init('shop', 'token', Env::SANDBOX, Validations::IGNORE_MISSING);
        $transport = new CurlTransport(new HttpDataSignature(), 'custom.example.com');

        $this->assertSame('custom.example.com', $this->readProtectedProperty($transport, 'url'));
    }

    public function testUseHttpsIsTrueForSandbox(): void {
        Riskified::init('shop', 'token', Env::SANDBOX, Validations::IGNORE_MISSING);
        $transport = new CurlTransport(new HttpDataSignature());

        $this->assertTrue($transport->use_https);
    }

    public function testUseHttpsIsTrueForProd(): void {
        Riskified::init('shop', 'token', Env::PROD, Validations::IGNORE_MISSING);
        $transport = new CurlTransport(new HttpDataSignature());

        $this->assertTrue($transport->use_https);
    }

    public function testUseHttpsIsFalseForDev(): void {
        Riskified::init('shop', 'token', Env::DEV, Validations::IGNORE_MISSING);
        $transport = new CurlTransport(new HttpDataSignature());

        $this->assertFalse($transport->use_https);
    }

    public function testOrderCallAfterAccountCallUsesDefaultHost(): void {
        Riskified::init('shop', 'token', Env::PROD, Validations::SKIP);
        $transport = new CapturingTransport(new HttpDataSignature());

        $transport->login(new Login());
        $this->assertSame('https://api.riskified.com/customers/login', $transport->lastUrl);

        $transport->submitOrder(new Order());
        $this->assertSame('https://wh.riskified.com/api/submit', $transport->lastUrl);
    }

    public function testOrderCallAfterDecideUsesDefaultHost(): void {
        Riskified::init('shop', 'token', Env::PROD, Validations::SKIP);
        $transport = new CapturingTransport(new HttpDataSignature());

        $transport->decideOrder(new Order());
        $this->assertSame('https://wh-sync.riskified.com/api/decide', $transport->lastUrl);

        $transport->submitOrder(new Order());
        $this->assertSame('https://wh.riskified.com/api/submit', $transport->lastUrl);
    }

    public function testOrderCallAfterDecoCallUsesDefaultHost(): void {
        Riskified::init('shop', 'token', Env::PROD, Validations::SKIP);
        $transport = new CapturingTransport(new HttpDataSignature());

        $transport->eligible(new Order());
        $this->assertSame('https://w.decopayments.com/api/eligible', $transport->lastUrl);

        $transport->submitOrder(new Order());
        $this->assertSame('https://wh.riskified.com/api/submit', $transport->lastUrl);
    }

    public function testCustomUrlIsKeptForDefaultFlowAfterOtherFlows(): void {
        Riskified::init('shop', 'token', Env::PROD, Validations::SKIP);
        $transport = new CapturingTransport(new HttpDataSignature(), 'custom.example.com');

        $transport->decideOrder(new Order());
        $transport->login(new Login());
        $transport->submitOrder(new Order());

        $this->assertSame('https://custom.example.com/api/submit', $transport->lastUrl);
        $this->assertSame('custom.example.com', $this->readProtectedProperty($transport, 'url'));
    }

    public function testHostIsRestoredWhenTheCallThrows(): void {
        Riskified::init('shop', 'token', Env::PROD, Validations::ALL);
        $transport = new CapturingTransport(new HttpDataSignature());

        try {
            $transport->decideOrder(new Order());
            $this->fail('Expected the empty order to fail validation');
        } catch (MultiplePropertiesException $e) {
            $this->assertSame('wh.riskified.com', $this->readProtectedProperty($transport, 'url'));
        }
    }
}
