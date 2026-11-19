<?php

namespace Riskified\Tests\OrderWebhook\Model;

use PHPUnit\Framework\TestCase;
use Riskified\OrderWebhook\Exception\MultiplePropertiesException;
use Riskified\OrderWebhook\Model\CryptoWallet;
use Riskified\OrderWebhook\Model\KycDetails;
use Riskified\OrderWebhook\Model\Recipient;

class RecipientTest extends TestCase {
    public function testDocumentedRecipientFieldsAreAcceptedAndSerialized(): void {
        $recipient = new Recipient([
            'email' => 'friend@example.com',
            'account_number' => '113401075211',
            'routing_number' => '345342175111',
            'bank_code' => 'ABCD-UYTD-34567000',
            'crypto_wallet' => new CryptoWallet(['address' => '0xabc', 'network' => 'ethereum']),
            'created_at' => new \DateTime('2024-01-01T10:00:00+00:00'),
            'updated_at' => '2024-01-02T10:00:00+00:00',
            'self_recipient' => false,
        ]);

        $this->assertTrue($recipient->validate(true));
        $this->assertSame([
            'email' => 'friend@example.com',
            'account_number' => '113401075211',
            'routing_number' => '345342175111',
            'bank_code' => 'ABCD-UYTD-34567000',
            'crypto_wallet' => ['address' => '0xabc', 'network' => 'ethereum'],
            'created_at' => '2024-01-01T10:00:00+00:00',
            'updated_at' => '2024-01-02T10:00:00+00:00',
            'self_recipient' => false,
        ], json_decode($recipient->toJson(), true));
    }

    public function testCryptoWalletRejectsAnotherModel(): void {
        $recipient = new Recipient(['crypto_wallet' => new KycDetails()]);

        $this->expectException(MultiplePropertiesException::class);
        $recipient->validate(false);
    }
}
