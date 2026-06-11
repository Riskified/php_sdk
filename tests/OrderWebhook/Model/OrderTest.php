<?php

namespace Riskified\Tests\OrderWebhook\Model;

use PHPUnit\Framework\TestCase;
use Riskified\OrderWebhook\Exception\MultiplePropertiesException;
use Riskified\OrderWebhook\Model\Order;

class OrderTest extends TestCase
{
    public function testPartnerSubMerchantIdIsNullByDefault(): void
    {
        $order = new Order();
        $this->assertNull($order->partner_sub_merchant_id);
    }

    public function testPartnerSubMerchantIdSerializesWithUnderscores(): void
    {
        $order = new Order();
        $order->partner_sub_merchant_id = 'merchant-abc';

        $json = $order->toJson();
        $this->assertStringContainsString('"partner_sub_merchant_id":"merchant-abc"', $json);
    }

    /**
     * @dataProvider acceptedAiAgentValues
     */
    public function testAiAgentAcceptsKnownValues(string $value): void
    {
        $order = new Order();
        $order->ai_agent = $value;

        // validate(false) checks formats only, skipping required-field enforcement.
        $this->assertTrue($order->validate(false));
    }

    public static function acceptedAiAgentValues(): array
    {
        return [
            ['chatgpt'],
            ['gemini'],
            ['copilot'],
            ['perplexity'],
        ];
    }

    public function testAiAgentRejectsUnknownValue(): void
    {
        $order = new Order();
        $order->ai_agent = 'unknown_bot';

        $this->expectException(MultiplePropertiesException::class);
        $order->validate(false);
    }

}
