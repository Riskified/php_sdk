<?php

namespace Riskified\Tests\OrderWebhook\Model;

use PHPUnit\Framework\TestCase;
use Riskified\Common\Riskified;
use Riskified\OrderWebhook\Model\Address;
use Riskified\OrderWebhook\Model\Checkout;
use Riskified\OrderWebhook\Model\Vendor;
use Riskified\OrderWebhook\Model\LineItem;

class LineItemTest extends TestCase {
    protected function tearDown(): void {
        Riskified::$legacy_wire_format = false;
    }

    private static function address(string $city): Address {
        return new Address([
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'city' => $city,
            'phone' => '5550100',
            'country' => 'United States',
            'country_code' => 'US',
        ]);
    }

    public function testDocumentedLineItemFieldsAreAcceptedAndSerialized(): void {
        $lineItem = new LineItem([
            'price' => 80.0,
            'quantity' => 1,
            'title' => 'Car rental',
            'drop_point' => 'locker-12',
            'authorized_payment' => 300.0,
            'damage_excess' => 1000.5,
        ]);

        $this->assertTrue($lineItem->validate(true));
        $json = json_decode($lineItem->toJson(), true);
        $this->assertSame('locker-12', $json['drop_point']);
        $this->assertSame(300.0, (float) $json['authorized_payment']);
        $this->assertSame(1000.5, $json['damage_excess']);
    }

    public function testAuthorizedPaymentAndAuthorizedPaymentsAreSeparateKeys(): void {
        $lineItem = new LineItem(['authorized_payment' => 1.5, 'authorized_payments' => 2.5]);

        $this->assertSame(1.5, $lineItem->authorized_payment);
        $this->assertSame(2.5, $lineItem->authorized_payments);
        $json = json_decode($lineItem->toJson(), true);
        $this->assertSame(1.5, $json['authorized_payment'], 'both set: each is sent as set');
        $this->assertSame(2.5, $json['authorized_payments']);
    }

    /**
     * @dataProvider authorizedPaymentKeys
     */
    public function testEitherAuthorizedPaymentKeyIsSentUnderBoth(string $key): void {
        $lineItem = new LineItem([$key => 300.5]);

        $json = json_decode($lineItem->toJson(), true);
        $this->assertSame(300.5, $json['authorized_payment']);
        $this->assertSame(300.5, $json['authorized_payments']);
        $this->assertNull($lineItem->{$key === 'authorized_payment' ? 'authorized_payments' : 'authorized_payment'});
    }

    /**
     * @return array<string, array{string}>
     */
    public static function authorizedPaymentKeys(): array {
        return ['documented key' => ['authorized_payment'], 'legacy key' => ['authorized_payments']];
    }

    public function testLegacyWireFormatSendsOnlyTheKeySet(): void {
        Riskified::$legacy_wire_format = true;
        $json = json_decode((new LineItem(['authorized_payments' => 300.5]))->toJson(), true);

        $this->assertArrayNotHasKey('authorized_payment', $json);
    }

    public function testSingleRideAddressesAreSentAsLists(): void {
        $lineItem = new LineItem(['pickup_address' => self::address('NYC'), 'dropoff_address' => self::address('JFK')]);

        $lineItem->validate(false);
        $json = json_decode($lineItem->toJson(), true);
        $this->assertSame('NYC', $json['pickup_address'][0]['city']);
        $this->assertSame('JFK', $json['dropoff_address'][0]['city']);
        $this->assertCount(1, $json['pickup_address']);
    }

    public function testAListOfRideAddressesIsAcceptedAndSentAsIs(): void {
        $lineItem = new LineItem(['pickup_address' => [self::address('NYC'), self::address('Newark')]]);

        $lineItem->validate(false);
        $json = json_decode($lineItem->toJson(), true);
        $this->assertSame(['NYC', 'Newark'], array_column($json['pickup_address'], 'city'));
    }

    public function testCheckoutLineItemsAlsoSendRideAddressesAsLists(): void {
        $checkout = new Checkout(['line_items' => [new LineItem(['pickup_address' => self::address('NYC')])]]);

        $json = json_decode($checkout->toJson(), true);
        $this->assertSame('NYC', $json['line_items'][0]['pickup_address'][0]['city']);
    }

    public function testLegacyWireFormatKeepsASingleRideAddress(): void {
        Riskified::$legacy_wire_format = true;
        $lineItem = new LineItem(['pickup_address' => self::address('NYC')]);

        $json = json_decode($lineItem->toJson(), true);
        $this->assertSame('NYC', $json['pickup_address']['city']);
    }

    public function testNumericCoordinatesAreSentAsFloats(): void {
        $lineItem = new LineItem(['latitude' => '40.7505', 'longitude' => -73.9934]);

        $lineItem->validate(false);
        $json = json_decode($lineItem->toJson(), true);
        $this->assertSame(40.7505, $json['latitude']);
        $this->assertSame(-73.9934, $json['longitude']);
    }

    public function testNonNumericCoordinatesPassThrough(): void {
        $lineItem = new LineItem(['latitude' => '40.8296 N', 'longitude' => '73.9262 W']);

        $lineItem->validate(false);
        $json = json_decode($lineItem->toJson(), true);
        $this->assertSame('40.8296 N', $json['latitude']);
        $this->assertSame('73.9262 W', $json['longitude']);
    }

    public function testANumericRatingIsSentAsAString(): void {
        $lineItem = new LineItem(['rating' => 4.5]);

        $lineItem->validate(false);
        $this->assertSame('4.5', json_decode($lineItem->toJson(), true)['rating']);
        $this->assertSame('4', json_decode((new LineItem(['rating' => 4]))->toJson(), true)['rating']);
    }

    public function testAStringRatingIsAcceptedAndSentAsIs(): void {
        $lineItem = new LineItem(['rating' => '4 stars']);

        $lineItem->validate(false);
        $this->assertSame('4 stars', json_decode($lineItem->toJson(), true)['rating']);
    }

    public function testLegacyWireFormatKeepsCoordinatesAndRatingAsSet(): void {
        Riskified::$legacy_wire_format = true;
        $lineItem = new LineItem(['latitude' => '40.7505', 'rating' => 4.5]);

        $json = json_decode($lineItem->toJson(), true);
        $this->assertSame('40.7505', $json['latitude']);
        $this->assertSame(4.5, $json['rating']);
    }

    public function testAVendorModelIsSentAsAnObject(): void {
        $lineItem = new LineItem(['vendor' => new Vendor(['vendor_id' => 'VNDR7890', 'vendor_name' => 'Acme'])]);

        $lineItem->validate(false);
        $json = json_decode($lineItem->toJson(), true);
        $this->assertSame(['vendor_id' => 'VNDR7890', 'vendor_name' => 'Acme'], $json['vendor']);
    }

    public function testAStringVendorIsStillAcceptedAndSentAsIs(): void {
        $lineItem = new LineItem(['vendor' => 'Acme']);

        $lineItem->validate(false);
        $this->assertSame('Acme', json_decode($lineItem->toJson(), true)['vendor']);
    }
}
