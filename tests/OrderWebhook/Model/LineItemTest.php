<?php

namespace Riskified\Tests\OrderWebhook\Model;

use PHPUnit\Framework\TestCase;
use Riskified\OrderWebhook\Exception\InvalidPropertyException;
use Riskified\OrderWebhook\Model\LineItem;

class LineItemTest extends TestCase {
    /**
     * Capture the E_USER_DEPRECATED notices raised while $action runs.
     *
     * PHPUnit 10.5 has no expectUserDeprecationMessage(), and letting the notice reach
     * PHPUnit's handler would report a suite-level deprecation for a deprecation this
     * test is asserting on purpose.
     *
     * @param callable $action
     * @return array<int, string> messages, in the order they were raised
     */
    private function captureDeprecations(callable $action): array {
        $messages = [];
        set_error_handler(
            function (int $errno, string $message) use (&$messages): bool {
                $messages[] = $message;

                return true;
            },
            E_USER_DEPRECATED
        );
        try {
            $action();
        } finally {
            restore_error_handler();
        }

        return $messages;
    }

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
        $this->assertSame(1, substr_count($json, 'dropoff_latit'));
    }

    /**
     * The correct spelling stays usable as a deprecated alias, so upgrading the SDK does
     * not break merchant code that already sets it - but it is stored, and therefore
     * sent, under the transposed wire name.
     */
    public function testCorrectlySpelledDropoffLatitudeIsAcceptedAsAnAlias(): void {
        $lineItem = new LineItem();

        $this->captureDeprecations(function () use ($lineItem): void {
            $lineItem->dropoff_latitude = 32.0853;
        });

        $this->assertSame(32.0853, $lineItem->dropoff_latitiude);
        $this->assertSame(32.0853, $lineItem->dropoff_latitude, 'the alias reads back too');
    }

    public function testAliasIsAlsoAcceptedByTheConstructor(): void {
        $lineItem = null;
        $this->captureDeprecations(function () use (&$lineItem): void {
            $lineItem = new LineItem([
                'price' => 10.0,
                'quantity' => '1',
                'title' => 'Ride',
                'dropoff_latitude' => 32.0853,
            ]);
        });

        $this->assertSame(32.0853, $lineItem->dropoff_latitiude);
    }

    /**
     * The whole point of the alias: one key on the wire, and it is the transposed one.
     */
    public function testAliasSerializesToTheTransposedKeyOnly(): void {
        $lineItem = new LineItem();

        $this->captureDeprecations(function () use ($lineItem): void {
            $lineItem->dropoff_latitude = 32.0853;
        });
        $json = $lineItem->toJson();

        $this->assertStringContainsString('"dropoff_latitiude":32.0853', $json);
        $this->assertStringNotContainsString('"dropoff_latitude"', $json);
        $this->assertSame(1, substr_count($json, 'dropoff_latit'), 'no duplicate key');
        $this->assertSame('{"dropoff_latitiude":32.0853}', $json);
    }

    public function testWritingThroughTheAliasRaisesADeprecation(): void {
        $lineItem = new LineItem();

        $actualMessages = $this->captureDeprecations(function () use ($lineItem): void {
            $lineItem->dropoff_latitude = 32.0853;
        });

        $this->assertCount(1, $actualMessages);
        $this->assertStringContainsString('LineItem->dropoff_latitude is deprecated', $actualMessages[0]);
        $this->assertStringContainsString('LineItem->dropoff_latitiude', $actualMessages[0]);
    }

    public function testWritingTheCanonicalFieldRaisesNoDeprecation(): void {
        $lineItem = new LineItem();

        $actualMessages = $this->captureDeprecations(function () use ($lineItem): void {
            $lineItem->dropoff_latitiude = 32.0853;
        });

        $this->assertSame([], $actualMessages);
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
