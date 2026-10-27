<?php

namespace Riskified\Tests\OrderWebhook\Model;

use PHPUnit\Framework\TestCase;
use Riskified\OrderWebhook\Model\KycDetails;

class KycDetailsTest extends TestCase {
    public function testDocumentedFieldsSerialize(): void {
        $kyc = new KycDetails([
            'vendor_name' => 'socure',
            'update_at' => '2025-03-11T09:42:11+00:00',
            'kyc_verified' => true,
            'kyc_type' => 'soft',
        ]);

        $this->assertTrue($kyc->validate(false));
        $this->assertSame(
            '{"vendor_name":"socure","update_at":"2025-03-11T09:42:11+00:00","kyc_verified":true,"kyc_type":"soft"}',
            $kyc->toJson()
        );
    }

    public function testEveryFieldIsOptional(): void {
        $this->assertTrue((new KycDetails())->validate(true));
    }
}
