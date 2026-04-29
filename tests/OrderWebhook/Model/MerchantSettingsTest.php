<?php

namespace Riskified\Tests\OrderWebhook\Model;

use PHPUnit\Framework\TestCase;
use Riskified\OrderWebhook\Model\MerchantSettings;

class MerchantSettingsTest extends TestCase
{
    public function testSerializesSettingsArray(): void
    {
        $settings = new MerchantSettings([
            'settings' => [
                'notify_url' => 'https://example.com/hook',
                'mode' => 'active',
            ],
        ]);

        $json = $settings->toJson();
        $this->assertStringContainsString('"settings"', $json);
        $this->assertStringContainsString('"notify_url":"https://example.com/hook"', $json);
    }

    public function testValidateSucceedsForStringAssociativeSettings(): void
    {
        $settings = new MerchantSettings([
            'settings' => ['feature_x' => 'on'],
        ]);
        $settings->validate(false);
        $this->assertSame(['feature_x' => 'on'], $settings->settings);
    }
}
