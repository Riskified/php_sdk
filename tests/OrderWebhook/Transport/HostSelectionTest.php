<?php

namespace Riskified\Tests\OrderWebhook\Transport;

use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use Riskified\Common\Env;
use Riskified\Common\Riskified;
use Riskified\Common\Signature\HttpDataSignature;
use Riskified\Common\Validations;
use Riskified\OrderWebhook\Model\Login;
use Riskified\OrderWebhook\Model\Order;

/**
 * Host selection must be resolved per call, never remembered on the instance.
 *
 * A merchant commonly builds one transport and reuses it for a whole request. If an
 * account-host call (login, customer_create, ...) leaves the transport pointed at
 * api.riskified.com, every later order call on that instance cross-posts order data
 * to the Account Secure host - silently, with a 2xx.
 */
class HostSelectionTest extends TestCase {
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

    private function readProtectedProperty(object $object, string $property): mixed {
        // No setAccessible() call: it is a no-op since PHP 8.1 and deprecated in 8.5.
        return (new ReflectionProperty($object, $property))->getValue($object);
    }

    /**
     * Validations are skipped so these tests assert host routing only, not payloads.
     */
    private function prodTransport(): RecordingTransport {
        Riskified::init('shop.example.com', 'token', Env::PROD, Validations::SKIP);

        return new RecordingTransport(new HttpDataSignature());
    }

    public function testAccountCallThenOrderCallOnSameInstanceUsesOrdersHost(): void {
        $transport = $this->prodTransport();

        $transport->login(new Login());
        $transport->submitOrder(new Order());

        $this->assertSame(
            [
                'https://api.riskified.com/customers/login',
                'https://wh.riskified.com/api/submit',
            ],
            $transport->requestedUrls
        );
    }

    public function testDecideThenSubmitOnSameInstanceUsesOrdersHost(): void {
        $transport = $this->prodTransport();

        $transport->decideOrder(new Order());
        $transport->submitOrder(new Order());

        $this->assertSame(
            [
                'https://wh-sync.riskified.com/api/decide',
                'https://wh.riskified.com/api/submit',
            ],
            $transport->requestedUrls
        );
    }

    /**
     * /api/decide is a sync-host path whichever entry point reaches it. The C# reference
     * has exactly one /api/decide call site, unconditionally FlowStrategy.Sync
     * (sdk_net @ 9165cf5, Orders/OrdersGateway.cs:142) - there is no checkout-specific
     * decide that legitimately uses the default host.
     */
    public function testCheckoutDecideUsesTheSyncHost(): void {
        $transport = $this->prodTransport();

        $transport->checkout_decide(new Order());
        $transport->submitOrder(new Order());

        $this->assertSame(
            [
                'https://wh-sync.riskified.com/api/decide',
                'https://wh.riskified.com/api/submit',
            ],
            $transport->requestedUrls
        );
    }

    public function testCheckoutDecideAndDecideOrderAgreeOnTheHost(): void {
        $transport = $this->prodTransport();

        $transport->decideOrder(new Order());
        $transport->checkout_decide(new Order());

        $this->assertSame(
            [
                'https://wh-sync.riskified.com/api/decide',
                'https://wh-sync.riskified.com/api/decide',
            ],
            $transport->requestedUrls
        );
    }

    public function testCheckoutDecideFallsBackToTheDefaultHostInSandbox(): void {
        Riskified::init('shop.example.com', 'token', Env::SANDBOX, Validations::SKIP);
        $transport = new RecordingTransport(new HttpDataSignature());

        $transport->checkout_decide(new Order());

        $this->assertSame(['https://sandbox.riskified.com/api/decide'], $transport->requestedUrls);
    }

    public function testDecoCallThenOrderCallOnSameInstanceUsesOrdersHost(): void {
        $transport = $this->prodTransport();

        $transport->eligible(new Order());
        $transport->opt_in(new Order());
        $transport->createOrder(new Order());

        $this->assertSame(
            [
                'https://w.decopayments.com/api/eligible',
                'https://w.decopayments.com/api/opt_in',
                'https://wh.riskified.com/api/create',
            ],
            $transport->requestedUrls
        );
    }

    public function testAllFourHostFamiliesResolveOnOneInstance(): void {
        $transport = $this->prodTransport();

        $transport->submitOrder(new Order());
        $transport->decideOrder(new Order());
        $transport->checkout_decide(new Order());
        $transport->login(new Login());
        $transport->eligible(new Order());
        $transport->updateOrder(new Order());

        $this->assertSame(
            [
                'https://wh.riskified.com/api/submit',
                'https://wh-sync.riskified.com/api/decide',
                'https://wh-sync.riskified.com/api/decide',
                'https://api.riskified.com/customers/login',
                'https://w.decopayments.com/api/eligible',
                'https://wh.riskified.com/api/update',
            ],
            $transport->requestedUrls
        );
    }

    public function testAccountCallDoesNotMutateConfiguredUrl(): void {
        $transport = $this->prodTransport();
        $expectedUrl = $this->readProtectedProperty($transport, 'url');

        $transport->login(new Login());

        $this->assertSame('wh.riskified.com', $expectedUrl);
        $this->assertSame($expectedUrl, $this->readProtectedProperty($transport, 'url'));
    }

    public function testSandboxHasNoSyncHostSoDecideFallsBackToDefault(): void {
        Riskified::init('shop.example.com', 'token', Env::SANDBOX, Validations::SKIP);
        $transport = new RecordingTransport(new HttpDataSignature());

        // Sandbox has no 'sync' entry; the contract says it falls back to the default
        // host. docs/flows/00-shared-contract.md section 3.
        $transport->decideOrder(new Order());
        $transport->submitOrder(new Order());

        $this->assertSame(
            [
                'https://sandbox.riskified.com/api/decide',
                'https://sandbox.riskified.com/api/submit',
            ],
            $transport->requestedUrls
        );
    }

    public function testCustomUrlStillServesCallsWithNoFlowStrategy(): void {
        Riskified::init('shop.example.com', 'token', Env::PROD, Validations::SKIP);
        $transport = new RecordingTransport(new HttpDataSignature(), 'mock.example.com');

        $transport->submitOrder(new Order());

        $this->assertSame(['https://mock.example.com/api/submit'], $transport->requestedUrls);
    }

    /**
     * @dataProvider accountHostMethods
     */
    public function testEveryAccountMethodIsFollowedByAnOrderOnTheOrdersHost(
        string $method,
        string $endpoint
    ): void {
        $transport = $this->prodTransport();

        $transport->{$method}(new Login());
        $transport->submitOrder(new Order());

        $this->assertSame(
            [
                'https://api.riskified.com/customers/' . $endpoint,
                'https://wh.riskified.com/api/submit',
            ],
            $transport->requestedUrls
        );
    }

    /**
     * @return array<int, array<int, string>>
     */
    public static function accountHostMethods(): array {
        return [
            ['login', 'login'],
            ['customerCreate', 'customer_create'],
            ['verification', 'verification'],
            ['customerUpdate', 'customer_update'],
            ['logout', 'logout'],
            ['resetPasswordRequest', 'reset_password'],
            ['wishlistChanges', 'wishlist'],
            ['redeem', 'redeem'],
        ];
    }
}
