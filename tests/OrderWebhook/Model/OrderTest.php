<?php

namespace Riskified\Tests\OrderWebhook\Model;

use PHPUnit\Framework\TestCase;
use Riskified\Common\Riskified;
use Riskified\OrderWebhook\Exception\MultiplePropertiesException;
use Riskified\OrderWebhook\Model\Address;
use Riskified\OrderWebhook\Model\ChargeFreePaymentDetails;
use Riskified\OrderWebhook\Model\Checkout;
use Riskified\OrderWebhook\Model\Order;

class OrderTest extends TestCase {
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

    public function testPartnerSubMerchantIdIsNullByDefault(): void {
        $order = new Order();
        $this->assertNull($order->partner_sub_merchant_id);
    }

    public function testPartnerSubMerchantIdSerializesWithUnderscores(): void {
        $order = new Order();
        $order->partner_sub_merchant_id = 'merchant-abc';

        $json = $order->toJson();
        $this->assertStringContainsString('"partner_sub_merchant_id":"merchant-abc"', $json);
    }

    /**
     * @dataProvider acceptedAiAgentValues
     */
    public function testAiAgentAcceptsKnownValues(string $value): void {
        $order = new Order();
        $order->ai_agent = $value;

        // validate(false) checks formats only, skipping required-field enforcement.
        $this->assertTrue($order->validate(false));
    }

    public static function acceptedAiAgentValues(): array {
        return [
            ['chatgpt'],
            ['gemini'],
            ['copilot'],
            ['perplexity'],
        ];
    }

    public function testAiAgentRejectsUnknownValue(): void {
        $order = new Order();
        $order->ai_agent = 'unknown_bot';

        $this->expectException(MultiplePropertiesException::class);
        $order->validate(false);
    }

    public function testInvoiceIdSerializes(): void {
        $order = new Order(['invoice_id' => 'north-america']);

        $order->validate(false);
        $this->assertStringContainsString('"invoice_id":"north-america"', $order->toJson());
    }

    public function testCheckoutAcceptsInvoiceId(): void {
        $checkout = new Checkout(['invoice_id' => 'europe']);

        $checkout->validate(false);
        $this->assertSame('europe', $checkout->invoice_id);
    }

    public function testASingleAddressIsSentAsAList(): void {
        $order = new Order(['billing_address' => self::address('NYC'), 'shipping_address' => self::address('LA')]);

        $order->validate(false);
        $json = json_decode($order->toJson(), true);
        $this->assertSame([['city' => 'NYC']], self::cities($json['billing_address']));
        $this->assertSame([['city' => 'LA']], self::cities($json['shipping_address']));
        $this->assertInstanceOf(Address::class, $order->billing_address, 'the property keeps what was set');
    }

    public function testAListOfAddressesIsAcceptedAndSentAsIs(): void {
        $order = new Order(['billing_address' => [self::address('NYC'), self::address('LA')]]);

        $order->validate(false);
        $json = json_decode($order->toJson(), true);
        $this->assertSame([['city' => 'NYC'], ['city' => 'LA']], self::cities($json['billing_address']));
    }

    public function testAnInvalidAddressInAListStillFailsValidation(): void {
        $address = self::address('NYC');
        $address->country_code = 'USA';
        $order = new Order(['billing_address' => [$address]]);

        $this->expectException(MultiplePropertiesException::class);
        $order->validate(false);
    }

    public function testCheckoutKeepsASingleAddress(): void {
        $checkout = new Checkout(['billing_address' => self::address('NYC')]);

        $json = json_decode($checkout->toJson(), true);
        $this->assertSame(['city' => 'NYC'], self::cities([$json['billing_address']])[0]);
    }

    public function testLegacyWireFormatKeepsASingleAddress(): void {
        Riskified::$legacy_wire_format = true;
        $order = new Order(['billing_address' => self::address('NYC')]);

        $json = json_decode($order->toJson(), true);
        $this->assertSame('NYC', $json['billing_address']['city']);
    }

    public function testASingleChargeFreePaymentDetailsIsSentAsAList(): void {
        $order = new Order([
            'charge_free_payment_details' => new ChargeFreePaymentDetails(['gateway' => 'gift_card', 'amount' => '5.00']),
        ]);

        $order->validate(false);
        $json = json_decode($order->toJson(), true);
        $this->assertSame([['gateway' => 'gift_card', 'amount' => '5.00']], $json['charge_free_payment_details']);
    }

    public function testAListOfChargeFreePaymentDetailsIsSentAsIs(): void {
        $checkout = new Checkout([
            'charge_free_payment_details' => [
                new ChargeFreePaymentDetails(['gateway' => 'gift_card', 'amount' => '5.00']),
                new ChargeFreePaymentDetails(['gateway' => 'store_credit', 'amount' => '2.00']),
            ],
        ]);

        $checkout->validate(false);
        $json = json_decode($checkout->toJson(), true);
        $this->assertSame(['gift_card', 'store_credit'], array_column($json['charge_free_payment_details'], 'gateway'));
    }

    public function testLegacyWireFormatKeepsASingleChargeFreePaymentDetails(): void {
        Riskified::$legacy_wire_format = true;
        $order = new Order([
            'charge_free_payment_details' => new ChargeFreePaymentDetails(['gateway' => 'gift_card', 'amount' => '5.00']),
        ]);

        $json = json_decode($order->toJson(), true);
        $this->assertSame('gift_card', $json['charge_free_payment_details']['gateway']);
    }

    /**
     * @param list<array<string, mixed>> $addresses
     * @return list<array{city: mixed}>
     */
    private static function cities(array $addresses): array {
        return array_map(function ($address) {
            return ['city' => $address['city']];
        }, $addresses);
    }
}
