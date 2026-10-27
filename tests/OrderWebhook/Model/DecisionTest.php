<?php

namespace Riskified\Tests\OrderWebhook\Model;

use PHPUnit\Framework\TestCase;
use Riskified\OrderWebhook\Model\Decision;
use Riskified\OrderWebhook\Model\DecisionDetails;

class DecisionTest extends TestCase {
    public function testGatewaySerializes(): void {
        $decision = new Decision([
            'id' => 'order-1',
            'decision' => new DecisionDetails(['external_status' => 'approved']),
            'gateway' => 'adyen_3ds',
        ]);

        $decision->validate(false);
        $this->assertStringContainsString('"gateway":"adyen_3ds"', $decision->toJson());
    }
}
