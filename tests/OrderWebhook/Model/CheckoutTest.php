<?php

namespace Riskified\Tests\OrderWebhook\Model;

use PHPUnit\Framework\TestCase;
use Riskified\OrderWebhook\Model\Checkout;
use Riskified\OrderWebhook\Model\Order;

class CheckoutTest extends TestCase
{
    public function testCheckoutIsInstanceOfOrder(): void
    {
        $checkout = new Checkout();
        $this->assertInstanceOf(Order::class, $checkout);
    }

    public function testMinimalCheckoutValidatesLikeOrder(): void
    {
        $checkout = new Checkout(OrderTestFixtures::minimalOrderProps());
        $checkout->validate(false);
        $this->assertSame('order-1', $checkout->id);
    }
}
