<?php

namespace Riskified\Tests\OrderWebhook\Model;

use PHPUnit\Framework\TestCase;
use Riskified\OrderWebhook\Model\Verification;
use Riskified\OrderWebhook\Model\VerificationSessionDetails;

class VerificationTest extends TestCase
{
    /**
     * @return array<string, mixed>
     */
    private function minimalVerificationProps(): array
    {
        return [
            'verified_at' => '2024-06-01T12:00:00+00:00',
            'event_id' => 'evt-123',
            'status' => 'success',
        ];
    }

    public function testOptionalEmailAndVendorAreNullByDefault(): void
    {
        $verification = new Verification($this->minimalVerificationProps());

        $this->assertNull($verification->email);
        $this->assertNull($verification->vendor_name);
    }

    public function testSerializesRequiredFields(): void
    {
        $verification = new Verification($this->minimalVerificationProps());
        $json = $verification->toJson();

        $this->assertStringContainsString('"event_id":"evt-123"', $json);
        $this->assertStringContainsString('"status":"success"', $json);
    }

    public function testNestedVerificationSessionDetailsSerializes(): void
    {
        $verification = new Verification($this->minimalVerificationProps() + [
            'verification_session_details' => new VerificationSessionDetails([
                'cart_token' => 'cart-abcd',
                'browser_ip' => '10.0.0.1',
            ]),
        ]);

        $json = $verification->toJson();
        $this->assertStringContainsString('"verification_session_details"', $json);
        $this->assertStringContainsString('"cart_token":"cart-abcd"', $json);
    }
}
