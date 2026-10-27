<?php

namespace Riskified\Tests\OrderWebhook\Model;

use PHPUnit\Framework\TestCase;
use Riskified\OrderWebhook\Model\AuthenticationResult;
use Riskified\OrderWebhook\Model\PaymentDetails;

class PaymentDetailsTest extends TestCase {
    public function testPaymentTypeIsNullByDefault(): void {
        $details = new PaymentDetails();
        $this->assertNull($details->payment_type);
    }

    public function testPaymentTypeSerializesToCard(): void {
        $details = new PaymentDetails();
        $details->payment_type = 'card';
        $this->assertStringContainsString('"payment_type":"card"', $details->toJson());
    }

    public function testPaymentTypeSerializesToPaypal(): void {
        $details = new PaymentDetails();
        $details->payment_type = 'paypal';
        $this->assertStringContainsString('"payment_type":"paypal"', $details->toJson());
    }

    public function testPaymentTypeSerializesToBankTransfer(): void {
        $details = new PaymentDetails();
        $details->payment_type = 'bank_transfer';
        $this->assertStringContainsString('"payment_type":"bank_transfer"', $details->toJson());
    }

    public function testAuthenticationResultNestedObjectSerializes(): void {
        $details = new PaymentDetails();
        $details->authentication_result = new AuthenticationResult([
            'eci' => '05',
            'liability_shift' => false,
            'trans_status' => 'A',
        ]);

        $json = $details->toJson();
        $this->assertStringContainsString('"authentication_result"', $json);
        $this->assertStringContainsString('"eci":"05"', $json);
        $this->assertStringContainsString('"trans_status":"A"', $json);
    }

    public function testDocumentedPaymentFieldsAreAcceptedAndSerialized(): void {
        $details = new PaymentDetails([
            'id' => 'pay-1',
            'mid' => 'mid-1',
            'acquirer_bin' => '411111',
            'billing_address_id' => 'addr-1',
            'credit_card_country' => 'US',
            'token' => 'tok-1',
            'acquirer_region' => 'EU',
            'authorization_type' => 'verification',
            'expiry_month' => 12,
            'expiry_year' => 2030,
            'payment_frequency' => 4,
            'initial_payment_amount' => 25.5,
        ]);

        $this->assertTrue($details->validate(false));
        $this->assertSame([
            'id' => 'pay-1',
            'mid' => 'mid-1',
            'acquirer_bin' => '411111',
            'billing_address_id' => 'addr-1',
            'credit_card_country' => 'US',
            'token' => 'tok-1',
            'acquirer_region' => 'EU',
            'authorization_type' => 'verification',
            'expiry_month' => 12,
            'expiry_year' => 2030,
            'payment_frequency' => 4,
            'initial_payment_amount' => 25.5,
        ], json_decode($details->toJson(), true));
    }

    public function testDocumentedPaymentFieldsAreOptional(): void {
        $this->assertTrue((new PaymentDetails())->validate(true));
    }
}
