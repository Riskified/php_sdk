<?php

namespace Riskified\Tests\OrderWebhook\Model;

use PHPUnit\Framework\TestCase;
use Riskified\OrderWebhook\Model\MerchantSettings;

class MerchantSettingsTest extends TestCase {
    public function testSerializesSettingsArray(): void {
        $expected = [
            'settings' => [
                'notify_url' => 'https://example.com/hook',
                'mode' => 'active',
            ],
        ];
        $settings = new MerchantSettings($expected);

        $json = $settings->toJson();
        $this->assertSame($expected, json_decode($json, true));
    }

    public function testValidateSucceedsForStringAssociativeSettings(): void {
        $settings = new MerchantSettings([
            'settings' => ['feature_x' => 'on'],
        ]);
        $settings->validate(false);
        $this->assertSame(['feature_x' => 'on'], $settings->settings);
    }
}
