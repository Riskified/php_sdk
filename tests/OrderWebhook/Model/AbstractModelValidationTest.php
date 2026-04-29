<?php

namespace Riskified\Tests\OrderWebhook\Model;

use PHPUnit\Framework\TestCase;
use Riskified\OrderWebhook\Exception\MultiplePropertiesException;
use Riskified\OrderWebhook\Model\Order;

class AbstractModelValidationTest extends TestCase
{
    public function testRequiredFieldMissingThrowsException(): void
    {
        $this->expectException(MultiplePropertiesException::class);

        $order = new Order();
        $order->validate();
    }

    public function testOptionalFieldMissingDoesNotThrow(): void
    {
        $order = new Order(OrderTestFixtures::minimalOrderProps());

        $this->assertNull($order->partner_sub_merchant_id);
        $order->validate(false);
    }

    public function testNumberFieldWithInvalidValueThrowsException(): void
    {
        $props = OrderTestFixtures::minimalOrderProps();
        $props['number'] = '-1';
        $order = new Order($props);

        $this->expectException(MultiplePropertiesException::class);
        $order->validate(false);
    }

    public function testNumberFieldWithValidValueDoesNotThrow(): void
    {
        $props = OrderTestFixtures::minimalOrderProps();
        $props['number'] = '42';
        $order = new Order($props);
        $order->validate(false);
        $this->assertSame('42', $order->number);
    }

    public function testStringRegexMismatchThrowsException(): void
    {
        $props = OrderTestFixtures::minimalOrderProps();
        $props['currency'] = 'USDD';
        $order = new Order($props);

        $this->expectException(MultiplePropertiesException::class);
        $order->validate(false);
    }

    public function testStringRegexMatchDoesNotThrow(): void
    {
        $props = OrderTestFixtures::minimalOrderProps();
        $props['currency'] = 'USD';
        $order = new Order($props);
        $order->validate(false);
        $this->assertSame('USD', $order->currency);
    }
}
