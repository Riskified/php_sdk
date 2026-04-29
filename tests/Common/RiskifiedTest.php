<?php

namespace Riskified\Tests\Common;

use PHPUnit\Framework\TestCase;
use Riskified\Common\Env;
use Riskified\Common\Riskified;
use Riskified\Common\Validations;

class RiskifiedTest extends TestCase
{
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

    public function testGetHostByEnvForSandbox(): void
    {
        Riskified::init('shop', 'token', Env::SANDBOX, Validations::IGNORE_MISSING);
        $hosts = Riskified::getHostByEnv();
        $this->assertSame('sandbox.riskified.com', $hosts['default']);
        $this->assertSame('api-sandbox.riskified.com', $hosts['account']);
    }

    public function testGetHostByEnvForProd(): void
    {
        Riskified::init('shop', 'token', Env::PROD, Validations::IGNORE_MISSING);
        $hosts = Riskified::getHostByEnv();
        $this->assertSame('wh.riskified.com', $hosts['default']);
        $this->assertSame('wh-sync.riskified.com', $hosts['sync']);
        $this->assertSame('api.riskified.com', $hosts['account']);
        $this->assertSame('w.decopayments.com', $hosts['deco']);
    }

    public function testGetHostByEnvForDev(): void
    {
        Riskified::init('shop', 'token', Env::DEV, Validations::IGNORE_MISSING);
        $hosts = Riskified::getHostByEnv();
        $this->assertSame('localhost:3000', $hosts['default']);
    }

    public function testGetHostByEnvDefaultsToSandboxWhenEnvIsNull(): void
    {
        Riskified::init('shop', 'token', Env::SANDBOX, Validations::IGNORE_MISSING);
        Riskified::$env = null;
        $hosts = Riskified::getHostByEnv();
        $this->assertSame('sandbox.riskified.com', $hosts['default']);
    }

    public function testGetHostReturnsDefaultHost(): void
    {
        Riskified::init('shop', 'token', Env::SANDBOX, Validations::IGNORE_MISSING);
        $this->assertSame('sandbox.riskified.com', Riskified::getHost());
    }

    public function testGetHostReturnsFlowStrategyHost(): void
    {
        Riskified::init('shop', 'token', Env::PROD, Validations::IGNORE_MISSING);
        $this->assertSame('wh-sync.riskified.com', Riskified::getHost('sync'));
    }

    public function testGetHostFallsBackToDefaultForMissingStrategy(): void
    {
        Riskified::init('shop', 'token', Env::SANDBOX, Validations::IGNORE_MISSING);
        $this->assertSame('sandbox.riskified.com', Riskified::getHost('nonexistent'));
    }
}
