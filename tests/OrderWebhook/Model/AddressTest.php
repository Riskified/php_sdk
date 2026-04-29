<?php

namespace Riskified\Tests\OrderWebhook\Model;

use PHPUnit\Framework\TestCase;
use Riskified\OrderWebhook\Exception\MultiplePropertiesException;
use Riskified\OrderWebhook\Model\Address;

class AddressTest extends TestCase
{
    /**
     * @return array<string, mixed>
     */
    private function minimalAddressProps(): array
    {
        return [
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'city' => 'NYC',
            'phone' => '5550100',
            'country' => 'United States',
            'country_code' => 'US',
        ];
    }

    public function testOptionalIdIsNullByDefault(): void
    {
        $address = new Address($this->minimalAddressProps());
        $this->assertNull($address->id);
    }

    public function testSerializesSnakeCaseFields(): void
    {
        $address = new Address($this->minimalAddressProps() + [
            'province_code' => 'NY',
            'address1' => '1 Main St',
        ]);

        $json = $address->toJson();
        $this->assertStringContainsString('"country_code":"US"', $json);
        $this->assertStringContainsString('"province_code":"NY"', $json);
        $this->assertStringContainsString('"address1":"1 Main St"', $json);
    }

    public function testInvalidCountryCodeFormatFailsValidation(): void
    {
        $props = $this->minimalAddressProps();
        $props['country_code'] = 'USA';

        $address = new Address($props);

        $this->expectException(MultiplePropertiesException::class);
        $address->validate(false);
    }
}
