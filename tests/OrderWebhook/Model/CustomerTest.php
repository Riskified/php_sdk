<?php

namespace Riskified\Tests\OrderWebhook\Model;

use PHPUnit\Framework\TestCase;
use Riskified\OrderWebhook\Exception\MultiplePropertiesException;
use Riskified\OrderWebhook\Model\CryptoWallet;
use Riskified\OrderWebhook\Model\Customer;
use Riskified\OrderWebhook\Model\KycDetails;
use Riskified\OrderWebhook\Model\SocialDetails;

class CustomerTest extends TestCase {
    public function testDocumentedCustomerFieldsAreAcceptedAndSerialized(): void {
        $customer = new Customer([
            'email' => 'buyer@example.com',
            'first_name' => 'Test',
            'last_name' => 'User',
            'buyer_anonymous_id' => 'anon-1',
            'referrer_customer_id' => 'cust-0',
            'social_signup_type' => 'google',
            'has_defaulted' => false,
            'kyc_verified' => true,
            'phone_mandatory' => true,
            'spending_limit' => 1500.5,
            'kyc_details' => [new KycDetails(['vendor_name' => 'socure', 'kyc_verified' => true])],
            'crypto_wallets' => [new CryptoWallet(['address' => '0xabc', 'network' => 'ethereum'])],
        ]);

        $this->assertTrue($customer->validate(true));
        $this->assertSame([
            'email' => 'buyer@example.com',
            'first_name' => 'Test',
            'last_name' => 'User',
            'buyer_anonymous_id' => 'anon-1',
            'referrer_customer_id' => 'cust-0',
            'social_signup_type' => 'google',
            'has_defaulted' => false,
            'kyc_verified' => true,
            'phone_mandatory' => true,
            'spending_limit' => 1500.5,
            'kyc_details' => [['vendor_name' => 'socure', 'kyc_verified' => true]],
            'crypto_wallets' => [['address' => '0xabc', 'network' => 'ethereum']],
        ], json_decode($customer->toJson(), true));
    }

    public function testKycDetailsRejectsAnotherModel(): void {
        $customer = new Customer(['kyc_details' => [new SocialDetails()]]);

        $this->expectException(MultiplePropertiesException::class);
        $customer->validate(false);
    }

    public function testCryptoWalletsRejectsAnotherModel(): void {
        $customer = new Customer(['crypto_wallets' => [new KycDetails()]]);

        $this->expectException(MultiplePropertiesException::class);
        $customer->validate(false);
    }
}
