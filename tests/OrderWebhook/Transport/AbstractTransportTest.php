<?php

namespace Riskified\Tests\OrderWebhook\Transport;

use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use Riskified\Common\Env;
use Riskified\Common\Riskified;
use Riskified\Common\Signature\HttpDataSignature;
use Riskified\Common\Validations;
use Riskified\OrderWebhook\Transport\CurlTransport;

class AbstractTransportTest extends TestCase
{
    private function readProtectedProperty(object $object, string $property): mixed
    {
        $ref = new ReflectionProperty($object, $property);
        $ref->setAccessible(true);

        return $ref->getValue($object);
    }

    /** @var array<string, mixed> */
    private array $riskifiedSnapshot = [];

    protected function setUp(): void
    {
        $this->riskifiedSnapshot = [
            'domain' => Riskified::$domain,
            'auth_token' => Riskified::$auth_token,
            'env' => Riskified::$env,
            'validations' => Riskified::$validations,
        ];
    }

    protected function tearDown(): void
    {
        Riskified::$domain = $this->riskifiedSnapshot['domain'];
        Riskified::$auth_token = $this->riskifiedSnapshot['auth_token'];
        Riskified::$env = $this->riskifiedSnapshot['env'];
        Riskified::$validations = $this->riskifiedSnapshot['validations'];
    }

    public function testConstructionSetsUserAgent(): void
    {
        Riskified::init('shop', 'token', Env::SANDBOX, Validations::IGNORE_MISSING);
        $transport = new CurlTransport(new HttpDataSignature());

        $this->assertSame(
            'riskified_php_sdk/' . Riskified::VERSION,
            $this->readProtectedProperty($transport, 'user_agent')
        );
    }

    public function testConstructionSetsUrlFromRiskifiedGetHost(): void
    {
        Riskified::init('shop', 'token', Env::SANDBOX, Validations::IGNORE_MISSING);
        $transport = new CurlTransport(new HttpDataSignature());

        $this->assertSame(Riskified::getHost(), $this->readProtectedProperty($transport, 'url'));
    }

    public function testConstructionAcceptsCustomUrl(): void
    {
        Riskified::init('shop', 'token', Env::SANDBOX, Validations::IGNORE_MISSING);
        $transport = new CurlTransport(new HttpDataSignature(), 'custom.example.com');

        $this->assertSame('custom.example.com', $this->readProtectedProperty($transport, 'url'));
    }

    public function testUseHttpsIsTrueForSandbox(): void
    {
        Riskified::init('shop', 'token', Env::SANDBOX, Validations::IGNORE_MISSING);
        $transport = new CurlTransport(new HttpDataSignature());

        $this->assertTrue($transport->use_https);
    }

    public function testUseHttpsIsTrueForProd(): void
    {
        Riskified::init('shop', 'token', Env::PROD, Validations::IGNORE_MISSING);
        $transport = new CurlTransport(new HttpDataSignature());

        $this->assertTrue($transport->use_https);
    }

    public function testUseHttpsIsFalseForDev(): void
    {
        Riskified::init('shop', 'token', Env::DEV, Validations::IGNORE_MISSING);
        $transport = new CurlTransport(new HttpDataSignature());

        $this->assertFalse($transport->use_https);
    }
}
