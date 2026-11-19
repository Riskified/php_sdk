<?php

namespace Riskified\Tests\OrderWebhook\Model;

use PHPUnit\Framework\TestCase;
use Riskified\OrderWebhook\Model\SocialDetails;

class SocialDetailsTest extends TestCase {
    /**
     * @return array<string, mixed>
     */
    private function minimalSocialProps(): array {
        return ['network' => 'facebook', 'public_username' => 'testuser', 'id' => 'social-1'];
    }

    public function testMixedFieldSerializesAnAssociativeArrayAsAJsonObject(): void {
        $social = new SocialDetails($this->minimalSocialProps() + [
            'social_data' => ['followers' => 200, 'verified' => true, 'tags' => ['a', 'b']],
        ]);

        $this->assertTrue($social->validate(true));
        $this->assertStringContainsString(
            '"social_data":{"followers":200,"verified":true,"tags":["a","b"]}',
            $social->toJson()
        );
    }

    public function testMixedFieldAcceptsAnyValue(): void {
        foreach (['text', 42, 1.5, false, [1, 2]] as $value) {
            $social = new SocialDetails($this->minimalSocialProps() + ['social_data' => $value]);
            $this->assertTrue($social->validate(true));
        }
    }
}
