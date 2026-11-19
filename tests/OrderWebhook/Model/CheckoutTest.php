<?php

namespace Riskified\Tests\OrderWebhook\Model;

use PHPUnit\Framework\TestCase;
use Riskified\Common\Riskified;
use Riskified\OrderWebhook\Model\AuthorizationError;
use Riskified\OrderWebhook\Model\Checkout;
use Riskified\OrderWebhook\Model\Order;
use Riskified\OrderWebhook\Model\PaymentDetails;

class CheckoutTest extends TestCase {
    protected function tearDown(): void {
        Riskified::$legacy_wire_format = false;
    }

    private static function declined(): AuthorizationError {
        return new AuthorizationError(['created_at' => '2024-01-01T10:00:00+00:00', 'error_code' => 'card_declined']);
    }

    public function testCheckoutIsInstanceOfOrder(): void {
        $checkout = new Checkout();
        $this->assertInstanceOf(Order::class, $checkout);
    }

    public function testMinimalCheckoutValidatesLikeOrder(): void {
        $checkout = new Checkout(OrderTestFixtures::minimalOrderProps());
        $checkout->validate(false);
        $this->assertSame('order-1', $checkout->id);
    }

    public function testTheRootAuthorizationErrorIsCopiedIntoEachPaymentDetail(): void {
        $checkout = new Checkout([
            'authorization_error' => self::declined(),
            'payment_details' => [new PaymentDetails(['credit_card_bin' => '411111']), new PaymentDetails()],
        ]);

        $json = json_decode($checkout->toJson(), true);
        $this->assertSame('card_declined', $json['payment_details'][0]['authorization_error']['error_code']);
        $this->assertSame('card_declined', $json['payment_details'][1]['authorization_error']['error_code']);
        $this->assertSame('card_declined', $json['authorization_error']['error_code'], 'the root key is kept');
        $this->assertNull($checkout->payment_details[0]->authorization_error, 'the models are not changed');
    }

    public function testAPaymentDetailsOwnAuthorizationErrorIsKept(): void {
        $own = new AuthorizationError(['created_at' => '2024-01-01T10:00:00+00:00', 'error_code' => 'expired_card']);
        $checkout = new Checkout([
            'authorization_error' => self::declined(),
            'payment_details' => [new PaymentDetails(['authorization_error' => $own])],
        ]);

        $json = json_decode($checkout->toJson(), true);
        $this->assertSame('expired_card', $json['payment_details'][0]['authorization_error']['error_code']);
    }

    public function testASinglePaymentDetailsAlsoGetsTheAuthorizationError(): void {
        $checkout = new Checkout([
            'authorization_error' => self::declined(),
            'payment_details' => new PaymentDetails(['credit_card_bin' => '411111']),
        ]);

        $json = json_decode($checkout->toJson(), true);
        $this->assertSame('card_declined', $json['payment_details']['authorization_error']['error_code']);
    }

    public function testOrdersAndTheLegacyWireFormatDoNotCopyIt(): void {
        $props = ['authorization_error' => self::declined(), 'payment_details' => [new PaymentDetails()]];

        $this->assertSame([[]], json_decode((new Order($props))->toJson(), true)['payment_details']);
        Riskified::$legacy_wire_format = true;
        $this->assertSame([[]], json_decode((new Checkout($props))->toJson(), true)['payment_details']);
    }
}
