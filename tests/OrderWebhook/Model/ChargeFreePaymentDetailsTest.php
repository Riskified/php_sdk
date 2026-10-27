<?php

namespace Riskified\Tests\OrderWebhook\Model;

use PHPUnit\Framework\TestCase;
use Riskified\Common\Riskified;
use Riskified\OrderWebhook\Exception\MultiplePropertiesException;
use Riskified\OrderWebhook\Model\ChargeFreePaymentDetails;

class ChargeFreePaymentDetailsTest extends TestCase {
    protected function tearDown(): void {
        Riskified::$legacy_wire_format = false;
    }

    /**
     * @dataProvider numericAmounts
     * @param mixed $amount
     */
    public function testANumericAmountIsSentAsAFloat($amount): void {
        $details = new ChargeFreePaymentDetails(['gateway' => 'gift_card', 'amount' => $amount]);

        $this->assertTrue($details->validate(true));
        $this->assertSame(['gateway' => 'gift_card', 'amount' => 5.25], json_decode($details->toJson(), true));
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function numericAmounts(): array {
        return ['string' => ['5.25'], 'float' => [5.25]];
    }

    public function testANonNumericAmountPassesThrough(): void {
        $details = new ChargeFreePaymentDetails(['gateway' => 'gift_card', 'amount' => 'five']);

        $this->assertSame('five', json_decode($details->toJson(), true)['amount']);
    }

    public function testAmountIsStillRequired(): void {
        $details = new ChargeFreePaymentDetails(['gateway' => 'gift_card']);

        $this->expectException(MultiplePropertiesException::class);
        $details->validate(true);
    }

    public function testLegacyWireFormatKeepsTheAmountAsSet(): void {
        Riskified::$legacy_wire_format = true;
        $details = new ChargeFreePaymentDetails(['gateway' => 'gift_card', 'amount' => '5.25']);

        $this->assertSame('5.25', json_decode($details->toJson(), true)['amount']);
    }
}
