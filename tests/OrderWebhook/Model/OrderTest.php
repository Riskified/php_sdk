<?php

namespace Riskified\Tests\OrderWebhook\Model;

use PHPUnit\Framework\TestCase;
use Riskified\OrderWebhook\Model\Order;

class OrderTest extends TestCase
{
    public function testPartnerSubMerchantIdIsNullByDefault(): void
    {
        $order = new Order();
        $this->assertNull($order->partner_sub_merchant_id);
    }

    public function testPartnerSubMerchantIdSerializesWithUnderscores(): void
    {
        $order = new Order();
        $order->partner_sub_merchant_id = 'merchant-abc';

        $json = $order->toJson();
        $this->assertStringContainsString('"partner_sub_merchant_id":"merchant-abc"', $json);
    }
}
