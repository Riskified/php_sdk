<?php

namespace Riskified\Tests\OrderWebhook\Model;

use PHPUnit\Framework\TestCase;
use Riskified\OrderWebhook\Exception\InvalidPropertyException;
use Riskified\OrderWebhook\Model\LineItem;

class LineItemTest extends TestCase {
    /**
     * The contract wire name for a ride line item's dropoff latitude is
     * `dropoff_latitude`, spelled normally.
     *
     * Both OpenAPI specs declare it that way, with a description and an example. The C#
     * reference SDK sends a transposed `dropoff_latitiude` (`sdk_net` @ 9165cf5,
     * Riskified.SDK/Model/OrderElements/RideTicketLineItem.cs:100,
     * `[JsonProperty(PropertyName = "dropoff_latitiude")]`) - that is a defect in the C#
     * SDK, not the wire contract, and this SDK must not copy it.
     *
     * Guarded in both directions on purpose: an earlier revision of this branch renamed
     * the field to the transposition and asserted it, on a corpus claim that had been
     * inferred from the C# alone.
     */
    public function testDropoffLatitudeUsesTheContractWireName(): void {
        $lineItem = new LineItem();
        $lineItem->dropoff_latitude = 32.0853;

        $json = $lineItem->toJson();

        $this->assertStringContainsString('"dropoff_latitude":32.0853', $json);
        $this->assertStringNotContainsString('dropoff_latitiude', $json);
        $this->assertSame(1, substr_count($json, 'dropoff_latit'), 'no duplicate key');
        $this->assertSame('{"dropoff_latitude":32.0853}', $json);
    }

    /**
     * The transposed spelling is not a field, so it is rejected like any other unknown
     * property rather than silently accepted.
     */
    public function testTransposedDropoffLatitudeIsNotAField(): void {
        $lineItem = new LineItem();

        $this->expectException(InvalidPropertyException::class);
        $lineItem->dropoff_latitiude = 32.0853;
    }

    public function testUnknownPropertyStillThrows(): void {
        $lineItem = new LineItem();

        $this->expectException(InvalidPropertyException::class);
        $lineItem->dropoff_latitude_typo = 32.0853;
    }

    public function testUnknownPropertyInConstructorStillThrows(): void {
        $this->expectException(InvalidPropertyException::class);
        new LineItem(['not_a_field' => 'x']);
    }

    public function testReadingAnUnknownPropertyStillThrows(): void {
        $lineItem = new LineItem();

        $this->expectException(InvalidPropertyException::class);
        $lineItem->not_a_field;
    }

    /**
     * All four ride geolocation fields are spelled per the contract - none carries a typo.
     */
    public function testRideGeolocationFieldsUseTheirContractWireNames(): void {
        $lineItem = new LineItem([
            'pickup_latitude' => 32.0853,
            'pickup_longitude' => 34.7818,
            'dropoff_latitude' => 31.7683,
            'dropoff_longitude' => 34.9896,
        ]);

        $json = $lineItem->toJson();

        $this->assertStringContainsString('"pickup_latitude":32.0853', $json);
        $this->assertStringContainsString('"pickup_longitude":34.7818', $json);
        $this->assertStringContainsString('"dropoff_latitude":31.7683', $json);
        $this->assertStringContainsString('"dropoff_longitude":34.9896', $json);
        $this->assertStringNotContainsString('dropoff_latitiude', $json);
    }
}
