<?php

namespace Riskified\Tests\OrderWebhook\Model;

use PHPUnit\Framework\TestCase;
use Riskified\OrderWebhook\Model\AuthenticationResult;

class AuthenticationResultTest extends TestCase
{
    /**
     * @return array<string, mixed>
     */
    private function minimalAuthResultProps(): array
    {
        return [
            'eci' => '05',
            'liability_shift' => true,
        ];
    }

    public function testOptionalTransFieldsAreNullByDefault(): void
    {
        $result = new AuthenticationResult($this->minimalAuthResultProps());

        $this->assertNull($result->trans_status);
        $this->assertNull($result->trans_status_reason);
    }

    public function testSerializesNestedFieldNames(): void
    {
        $result = new AuthenticationResult($this->minimalAuthResultProps() + [
            'trans_status' => 'Y',
            'three_d_challenge' => false,
            'TRA_exemption' => true,
        ]);

        $json = $result->toJson();
        $this->assertStringContainsString('"trans_status":"Y"', $json);
        $this->assertStringContainsString('"three_d_challenge":false', $json);
        $this->assertStringContainsString('"TRA_exemption":true', $json);
    }
}
