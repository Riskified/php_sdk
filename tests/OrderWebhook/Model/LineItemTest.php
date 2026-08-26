<?php

namespace Riskified\Tests\OrderWebhook\Model;

use PHPUnit\Framework\TestCase;
use Riskified\OrderWebhook\Exception\InvalidPropertyException;
use Riskified\OrderWebhook\Model\LineItem;

class LineItemTest extends TestCase {
    /**
     * DO NOT "CORRECT" THIS SPELLING.
     *
     * The live wire name for a ride line item's dropoff latitude is the transposed
     * `dropoff_latitiude` (latit-i-ude). It is an upstream typo that the Riskified API
     * expects: the C# reference sends it too, from a correctly named C# property
     * (`sdk_net` @ 9165cf5, Riskified.SDK/Model/OrderElements/RideTicketLineItem.cs:101,
     * `[JsonProperty(PropertyName = "dropoff_latitiude")]`), and it is registered as a
     * deliberate non-derivable name in docs/flows/01-model-catalog.md section 5.
     *
     * Emitting the correctly spelled `dropoff_latitude` does not error - the field is
     * simply dropped, so ride-hailing dropoff geolocation silently stops arriving.
     */
    public function testDropoffLatitudeUsesTheLiveMisspelledWireName(): void {
        $lineItem = new LineItem();
        $lineItem->dropoff_latitiude = 32.0853;

        $json = $lineItem->toJson();

        $this->assertStringContainsString('"dropoff_latitiude":32.0853', $json);
        $this->assertStringNotContainsString('dropoff_latitude', $json);
    }

    /**
     * The correctly spelled name is not a field of the model, precisely so that a
     * caller who types it gets a loud exception rather than silent data loss.
     */
    public function testCorrectlySpelledDropoffLatitudeIsRejected(): void {
        $lineItem = new LineItem();

        $this->expectException(InvalidPropertyException::class);
        $lineItem->dropoff_latitude = 32.0853;
    }

    /**
     * The neighbouring geolocation fields are spelled normally - only the dropoff
     * latitude carries the typo.
     */
    public function testNeighbouringGeolocationFieldsAreSpelledNormally(): void {
        $lineItem = new LineItem([
            'pickup_latitude' => 32.0853,
            'pickup_longitude' => 34.7818,
            'dropoff_longitude' => 34.9896,
        ]);

        $json = $lineItem->toJson();

        $this->assertStringContainsString('"pickup_latitude":32.0853', $json);
        $this->assertStringContainsString('"pickup_longitude":34.7818', $json);
        $this->assertStringContainsString('"dropoff_longitude":34.9896', $json);
    }
}
