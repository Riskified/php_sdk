<?php

namespace Riskified\Tests\OrderWebhook\Model;

use DateTime;
use DateTimeImmutable;
use DateTimeZone;
use PHPUnit\Framework\TestCase;
use Riskified\OrderWebhook\Model\Order;

class AbstractModelDateTimeTest extends TestCase {
    public function testDateTimeSetAsPropertySerializesAsIsoString(): void {
        $order = new Order();
        $order->created_at = new DateTime('2024-01-01 10:00:00', new DateTimeZone('UTC'));

        $this->assertSame('{"created_at":"2024-01-01T10:00:00+00:00"}', $order->toJson());
    }

    public function testDateTimeSetAsPropertyPassesValidation(): void {
        $order = new Order();
        $order->created_at = new DateTime('2024-01-01 10:00:00', new DateTimeZone('UTC'));

        $this->assertTrue($order->validate(false));
    }

    public function testDateTimeSetAsPropertyReadsBackLikeTheConstructor(): void {
        $date = new DateTime('2024-01-01 10:00:00', new DateTimeZone('UTC'));
        $order = new Order(['created_at' => $date]);
        $order->updated_at = $date;

        $this->assertSame('2024-01-01T10:00:00+00:00', $order->created_at);
        $this->assertSame($order->created_at, $order->updated_at);
    }

    public function testDateTimeImmutableIsConvertedToo(): void {
        $date = new DateTimeImmutable('2024-01-01 10:00:00', new DateTimeZone('UTC'));
        $order = new Order(['created_at' => $date]);
        $order->updated_at = $date;

        $this->assertSame(
            '{"created_at":"2024-01-01T10:00:00+00:00","updated_at":"2024-01-01T10:00:00+00:00"}',
            $order->toJson()
        );
    }
}
