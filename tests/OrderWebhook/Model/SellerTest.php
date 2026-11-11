<?php

namespace Riskified\Tests\OrderWebhook\Model;

use PHPUnit\Framework\TestCase;
use Riskified\Common\Riskified;
use Riskified\OrderWebhook\Model\Customer;
use Riskified\OrderWebhook\Model\Seller;

class SellerTest extends TestCase {
    protected function tearDown(): void {
        Riskified::$legacy_wire_format = false;
    }

    private static function seller(): Seller {
        return new Seller([
            'customer' => new Customer(['email' => 'seller@example.com', 'first_name' => 'Jane', 'last_name' => 'Doe']),
            'starting_price' => 100.3,
        ]);
    }

    public function testStartingPriceIsSentAsAString(): void {
        $seller = self::seller();

        $seller->validate(false);
        $this->assertSame('100.3', json_decode($seller->toJson(), true)['starting_price']);
    }

    public function testLegacyWireFormatKeepsAFloatStartingPrice(): void {
        Riskified::$legacy_wire_format = true;

        $this->assertSame(100.3, json_decode(self::seller()->toJson(), true)['starting_price']);
    }
}
