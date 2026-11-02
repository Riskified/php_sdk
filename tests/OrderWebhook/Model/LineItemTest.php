<?php

namespace Riskified\Tests\OrderWebhook\Model;

use PHPUnit\Framework\TestCase;
use Riskified\OrderWebhook\Model\LineItem;

class LineItemTest extends TestCase {
    public function testDocumentedLineItemFieldsAreAcceptedAndSerialized(): void {
        $lineItem = new LineItem([
            'price' => 80.0,
            'quantity' => 1,
            'title' => 'Car rental',
            'drop_point' => 'locker-12',
            'authorized_payment' => 300.0,
            'damage_excess' => 1000.5,
        ]);

        $this->assertTrue($lineItem->validate(true));
        $json = json_decode($lineItem->toJson(), true);
        $this->assertSame('locker-12', $json['drop_point']);
        $this->assertSame(300.0, (float) $json['authorized_payment']);
        $this->assertSame(1000.5, $json['damage_excess']);
    }

    public function testAuthorizedPaymentAndAuthorizedPaymentsAreSeparateKeys(): void {
        $lineItem = new LineItem(['authorized_payment' => 1.5, 'authorized_payments' => 2.5]);

        $this->assertSame(1.5, $lineItem->authorized_payment);
        $this->assertSame(2.5, $lineItem->authorized_payments);
    }
}
