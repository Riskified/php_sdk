<?php

namespace Riskified\Tests\OrderWebhook\Model;

use PHPUnit\Framework\TestCase;
use Riskified\Common\Riskified;
use Riskified\OrderWebhook\Exception\MultiplePropertiesException;
use Riskified\OrderWebhook\Model\Decision;
use Riskified\OrderWebhook\Model\DecisionDetails;
use Riskified\OrderWebhook\Model\KycDetails;
use Riskified\OrderWebhook\Model\PaymentDetails;

class DecisionTest extends TestCase {
    protected function tearDown(): void {
        Riskified::$legacy_wire_format = false;
    }

    /**
     * @param mixed $paymentDetails
     */
    private static function decision($paymentDetails): Decision {
        return new Decision([
            'id' => 'order-1',
            'decision' => new DecisionDetails(['external_status' => 'approved']),
            'payment_details' => $paymentDetails,
        ]);
    }

    public function testGatewaySerializes(): void {
        $decision = new Decision([
            'id' => 'order-1',
            'decision' => new DecisionDetails(['external_status' => 'approved']),
            'gateway' => 'adyen_3ds',
        ]);

        $decision->validate(false);
        $this->assertStringContainsString('"gateway":"adyen_3ds"', $decision->toJson());
    }

    public function testASinglePaymentDetailsIsSentAsAList(): void {
        $decision = self::decision(new PaymentDetails(['authorization_id' => 'auth-1']));

        $decision->validate(false);
        $json = json_decode($decision->toJson(), true);
        $this->assertSame([['authorization_id' => 'auth-1']], $json['payment_details']);
    }

    public function testAListOfPaymentDetailsIsAcceptedAndSentAsIs(): void {
        $decision = self::decision([
            new PaymentDetails(['authorization_id' => 'auth-1']),
            new PaymentDetails(['authorization_id' => 'auth-2']),
        ]);

        $decision->validate(false);
        $json = json_decode($decision->toJson(), true);
        $this->assertSame(['auth-1', 'auth-2'], array_column($json['payment_details'], 'authorization_id'));
    }

    public function testAnotherModelInTheListStillFailsValidation(): void {
        $decision = self::decision([new KycDetails()]);

        $this->expectException(MultiplePropertiesException::class);
        $decision->validate(false);
    }

    public function testLegacyWireFormatKeepsASinglePaymentDetails(): void {
        Riskified::$legacy_wire_format = true;
        $decision = self::decision(new PaymentDetails(['authorization_id' => 'auth-1']));

        $json = json_decode($decision->toJson(), true);
        $this->assertSame(['authorization_id' => 'auth-1'], $json['payment_details']);
    }
}
