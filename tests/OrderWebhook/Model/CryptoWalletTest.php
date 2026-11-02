<?php

namespace Riskified\Tests\OrderWebhook\Model;

use PHPUnit\Framework\TestCase;
use Riskified\OrderWebhook\Model\CryptoWallet;

class CryptoWalletTest extends TestCase {
    public function testDocumentedFieldsSerialize(): void {
        $wallet = new CryptoWallet([
            'address' => '0xc0ffee254729296a45a3885639AC7E10F9d54979',
            'network' => 'ethereum',
            'type' => 'primary',
        ]);

        $this->assertTrue($wallet->validate(false));
        $this->assertSame(
            '{"address":"0xc0ffee254729296a45a3885639AC7E10F9d54979","network":"ethereum","type":"primary"}',
            $wallet->toJson()
        );
    }

    public function testEveryFieldIsOptional(): void {
        $this->assertTrue((new CryptoWallet())->validate(true));
    }
}
