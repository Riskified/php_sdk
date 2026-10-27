<?php

namespace Riskified\Tests\Golden;

use Riskified\OrderWebhook\Model;

/**
 * Fully populated sample payloads, one per flow. Dates are plain strings so the output is stable.
 */
final class GoldenSamples {
    const CREATED_AT = '2024-01-01T10:00:00+00:00';
    const UPDATED_AT = '2024-01-01T10:05:00+00:00';

    /**
     * Sample name => [transport method, model].
     * @return array<string, array{0: string, 1: Model\AbstractModel}>
     */
    public static function all(): array {
        return [
            'card_order' => ['submitOrder', self::cardOrder()],
            'paypal_order' => ['decideOrder', self::paypalOrder()],
            'bank_transfer_order' => ['submitOrder', self::bankTransferOrder()],
            'digital_order' => ['submitOrder', self::verticalOrder(self::digitalLineItem())],
            'travel_order' => ['submitOrder', self::travelOrder()],
            'ride_order' => ['submitOrder', self::verticalOrder(self::rideLineItem())],
            'event_order' => ['submitOrder', self::verticalOrder(self::eventLineItem())],
            'accommodation_order' => ['submitOrder', self::verticalOrder(self::accommodationLineItem())],
            'checkout_denied' => ['deniedCheckout', self::deniedCheckout()],
            'decision' => ['decisionOrder', self::decision()],
            'chargeback' => ['chargebackOrder', self::chargeback()],
            'order_1_14' => ['submitOrder', self::order114()],
            'decision_1_14' => ['decisionOrder', self::decision114()],
        ];
    }

    public static function cardOrder(): Model\Order {
        return new Model\Order(self::fullOrderProps([self::cardPayment()]));
    }

    public static function paypalOrder(): Model\Order {
        return new Model\Order(self::fullOrderProps([self::paypalPayment()]));
    }

    /**
     * Fields added in 1.14.0, on top of the card order. Each Release 2 step extends this sample;
     * the 1.13.x samples above stay unchanged.
     */
    public static function order114(): Model\Order {
        $props = self::fullOrderProps([
            self::cardPayment114(),
            self::digitalWalletPayment(),
            self::bnplPayment(),
            new Model\PaymentDetails(['payment_type' => 'gift_card']),
            self::bankTransferPayment114(),
        ]);
        $props['customer'] = self::customer114();
        $props['invoice_id'] = 'north-america';
        $props['shipping_address'] = self::address114();
        return new Model\Order($props);
    }

    public static function decision114(): Model\Decision {
        return new Model\Decision([
            'id' => 'order-1',
            'decision' => self::decisionDetails(),
            'payment_details' => self::cardPayment(),
            'gateway' => 'adyen_3ds',
        ]);
    }

    public static function address114(): Model\Address {
        $address = self::address();
        $address->neighborhood = 'Chelsea';
        return $address;
    }

    public static function customer114(): Model\Customer {
        $customer = self::customer();
        $customer->buyer_anonymous_id = 'anon-1';
        $customer->referrer_customer_id = 'cust-0';
        $customer->social_signup_type = 'google';
        $customer->has_defaulted = false;
        $customer->kyc_verified = true;
        $customer->phone_mandatory = true;
        $customer->spending_limit = 1500.5;
        $customer->kyc_details = [
            new Model\KycDetails([
                'vendor_name' => 'socure',
                'update_at' => '2025-03-11T09:42:11+00:00',
                'kyc_verified' => true,
                'kyc_type' => 'soft',
            ]),
        ];
        $customer->crypto_wallets = [
            new Model\CryptoWallet([
                'address' => '0xc0ffee254729296a45a3885639AC7E10F9d54979',
                'network' => 'ethereum',
                'type' => 'primary',
            ]),
        ];
        return $customer;
    }

    public static function bankTransferOrder(): Model\Order {
        return new Model\Order(self::fullOrderProps([self::bankTransferPayment()]));
    }

    public static function travelOrder(): Model\Order {
        $order = self::verticalOrder(self::travelLineItem());
        $order->passengers = [self::passenger()];
        return $order;
    }

    public static function verticalOrder(Model\LineItem $lineItem): Model\Order {
        $props = self::fullOrderProps([self::cardPayment()]);
        $props['line_items'] = [$lineItem];
        return new Model\Order($props);
    }

    /**
     * The authorization error sits on the checkout root, the form most integrations use today.
     */
    public static function deniedCheckout(): Model\Checkout {
        $props = self::fullOrderProps([self::cardPayment()]);
        $props['id'] = 'checkout-1';
        $props['authorization_error'] = self::authorizationError();
        return new Model\Checkout($props);
    }

    public static function decision(): Model\Decision {
        return new Model\Decision([
            'id' => 'order-1',
            'decision' => self::decisionDetails(),
            'payment_details' => self::cardPayment(),
        ]);
    }

    public static function chargeback(): Model\OrderChargeback {
        return new Model\OrderChargeback([
            'id' => 'order-1',
            'chargeback_details' => new Model\ChargebackDetails([
                'id' => 'cb-1',
                'chargeback_at' => '2024-02-01T09:00:00+00:00',
                'chargeback_currency' => 'USD',
                'chargeback_amount' => 120.5,
                'reason_code' => '4837',
                'reason_description' => 'No cardholder authorization',
                'type' => 'cb',
                'mid' => 'mid-123',
                'arn' => 'arn-456',
                'credit_card_company' => 'visa',
                'respond_by' => '2024-02-15T00:00:00+00:00',
                'fee_amount' => 15.0,
                'fee_currency' => 'USD',
                'card_issuer' => 'Example Bank',
                'gateway' => 'stripe',
                'cardholder' => 'Test User',
                'message' => 'Cardholder disputes the charge',
            ]),
            'fulfillment' => new Model\FulfillmentDetails([
                'created_at' => self::CREATED_AT,
                'fulfillment_id' => 'ful-1',
                'status' => 'success',
                'tracking_company' => 'UPS',
                'tracking_numbers' => '1Z999',
                'tracking_urls' => 'https://track.example.com/1Z999',
                'message' => 'Delivered',
                'receipt' => 'receipt-1',
                'line_items' => [self::physicalLineItem()],
            ]),
            'dispute_details' => new Model\DisputeDetails([
                'case_id' => 'case-1',
                'status' => 'open',
                'disputed_at' => '2024-02-01T09:00:00+00:00',
                'expected_resolution_date' => '2024-03-01T00:00:00+00:00',
                'dispute_type' => 'first_chargeback',
                'issuer_poc_phone_number' => '+15550000000',
            ]),
        ]);
    }

    /**
     * @param Model\PaymentDetails[] $paymentDetails
     * @return array<string, mixed>
     */
    public static function fullOrderProps(array $paymentDetails): array {
        return [
            'id' => 'order-1',
            'email' => 'buyer@example.com',
            'created_at' => self::CREATED_AT,
            'updated_at' => self::UPDATED_AT,
            'currency' => 'USD',
            'gateway' => 'stripe',
            'total_price' => 120.5,
            'browser_ip' => '203.0.113.10',
            'customer' => self::customer(),
            'line_items' => [self::physicalLineItem()],
            'name' => '#1001',
            'additional_emails' => ['alt@example.com'],
            'note' => 'Leave at the door',
            'number' => 1001,
            'order_number' => 1001,
            'cancel_reason' => 'customer',
            'cancelled_at' => '2024-01-02T10:00:00+00:00',
            'closed_at' => '2024-01-03T10:00:00+00:00',
            'cart_token' => 'cart-token-1',
            'device_id' => 'device-1',
            'checkout_token' => 'checkout-token-1',
            'token' => 'order-token-1',
            'referring_site' => 'https://ref.example.com',
            'confirmed' => true,
            'buyer_accepts_marketing' => false,
            'financial_status' => 'paid',
            'fulfillment_status' => 'fulfilled',
            'landing_site' => '/landing',
            'landing_site_ref' => 'ref-1',
            'location_id' => 'loc-1',
            'source' => 'desktop_web',
            'source_identifier' => 'src-1',
            'source_name' => 'web',
            'source_url' => 'https://shop.example.com',
            'subtotal_price' => 100.0,
            'taxes_included' => true,
            'total_discounts' => 5.0,
            'total_line_items_price' => 105.0,
            'total_price_usd' => 120.5,
            'total_tax' => 10.5,
            'total_weight' => 500.0,
            'user_id' => 'user-1',
            'processing_method' => 'direct',
            'checkout_id' => 'checkout-1',
            'tags' => 'vip',
            'vendor_id' => 'vendor-1',
            'vendor_name' => 'Example Vendor',
            'vendor_integration_type' => 'api',
            'order_type' => 'test',
            'partner_sub_merchant_id' => 'sub-merchant-1',
            'submission_reason' => 'manual_decision',
            'ai_agent' => 'chatgpt',
            'shipping_address' => self::address(),
            'billing_address' => self::address(),
            'payment_details' => $paymentDetails,
            'client_details' => self::clientDetails(),
            'discount_codes' => [
                new Model\DiscountCode([
                    'code' => 'SAVE5',
                    'amount' => 5.0,
                    'policy' => new Model\Policy(['evaluate' => true]),
                ]),
            ],
            'shipping_lines' => [
                new Model\ShippingLine([
                    'price' => 10.0,
                    'title' => 'Express',
                    'code' => 'EXP',
                    'company' => 'UPS',
                    'shipping_address_id' => 'addr-1',
                ]),
            ],
            'note_attributes' => [new Model\Attribute(['name' => 'gift', 'value' => 'yes'])],
            'tax_lines' => [self::taxLine()],
            'nocharge_amount' => new Model\RefundDetails([
                'refund_id' => 'refund-1',
                'amount' => 5.0,
                'currency' => 'USD',
                'sku' => 'SKU-1',
                'refunded_at' => self::UPDATED_AT,
                'reason' => 'goodwill',
            ]),
            'decision' => self::decisionDetails(),
            'charge_free_payment_details' => new Model\ChargeFreePaymentDetails([
                'gateway' => 'gift_card',
                'amount' => '5.00',
            ]),
        ];
    }

    public static function customer(): Model\Customer {
        return new Model\Customer([
            'email' => 'buyer@example.com',
            'first_name' => 'Test',
            'last_name' => 'User',
            'created_at' => '2023-06-01T00:00:00+00:00',
            'updated_at' => self::CREATED_AT,
            'id' => 'cust-1',
            'group_id' => 'group-1',
            'group_name' => 'Retail',
            'note' => 'Returning customer',
            'orders_count' => 3,
            'verified_email' => true,
            'verified_email_at' => '2023-06-01T00:00:00+00:00',
            'phone' => '+15551234567',
            'verified_phone' => true,
            'verified_phone_at' => '2023-06-01T00:00:00+00:00',
            'date_of_birth' => '1990-05-01',
            'gender' => 'female',
            'user_name' => 'testuser',
            'accepts_marketing' => true,
            'last_order_id' => 'order-0',
            'last_order_name' => '#1000',
            'state' => 'enabled',
            'total_spent' => 300.0,
            'tags' => 'loyal',
            'account_type' => 'registered',
            'first_purchase_at' => '2023-06-02T00:00:00+00:00',
            'linked_accounts' => 1,
            'default_address' => self::address(),
            'social' => [self::socialDetails()],
            'buy_attempts' => 4,
            'sell_attempts' => 0,
            'document_id' => 'doc-1',
            'document_type' => 'passport',
            'address' => self::address(),
        ]);
    }

    public static function address(): Model\Address {
        return new Model\Address([
            'id' => 'addr-1',
            'first_name' => 'Test',
            'last_name' => 'User',
            'city' => 'New York',
            'phone' => '+15551234567',
            'additional_phone' => '+15557654321',
            'country' => 'United States',
            'country_code' => 'US',
            'name' => 'Test User',
            'company' => 'Example Inc',
            'address1' => '1 Main St',
            'address2' => 'Apt 2',
            'province' => 'New York',
            'province_code' => 'NY',
            'zip' => '10001',
            'latitude' => 40.7128,
            'longitude' => -74.006,
            'verified_phone' => true,
        ]);
    }

    public static function clientDetails(): Model\ClientDetails {
        return new Model\ClientDetails([
            'accept_language' => 'en-US',
            'user_agent' => 'Mozilla/5.0',
            'browser_ip' => '203.0.113.10',
            'session_hash' => 'session-hash-1',
        ]);
    }

    public static function socialDetails(): Model\SocialDetails {
        return new Model\SocialDetails([
            'network' => 'facebook',
            'public_username' => 'testuser',
            'id' => 'social-1',
            'community_score' => 10,
            'profile_picture' => 'https://img.example.com/p.png',
            'email' => 'buyer@example.com',
            'bio' => 'Hello',
            'account_url' => 'https://facebook.com/testuser',
            'following' => 100,
            'followed' => 200,
            'posts' => 50,
            'auth_token' => 'social-token',
        ]);
    }

    public static function taxLine(): Model\TaxLine {
        return new Model\TaxLine(['price' => 10.5, 'rate' => 0.1, 'title' => 'VAT']);
    }

    public static function decisionDetails(): Model\DecisionDetails {
        return new Model\DecisionDetails([
            'external_status' => 'approved',
            'decided_at' => self::UPDATED_AT,
            'reason' => 'trusted customer',
            'amount' => 120.5,
            'currency' => 'USD',
            'notes' => 'Approved manually',
        ]);
    }

    public static function authorizationError(): Model\AuthorizationError {
        return new Model\AuthorizationError([
            'created_at' => self::CREATED_AT,
            'error_code' => 'card_declined',
            'drop_off' => false,
            'message' => 'Insufficient funds',
        ]);
    }

    public static function cardPayment(): Model\PaymentDetails {
        return new Model\PaymentDetails([
            'credit_card_bin' => '458011',
            'avs_result_code' => 'Y',
            'cvv_result_code' => 'M',
            'credit_card_number' => 'XXXX-XXXX-XXXX-1234',
            'credit_card_company' => 'visa',
            'credit_card_token' => 'cc-token-1',
            'installments' => 1,
            'stored_payment_created_at' => '2023-06-01T00:00:00+00:00',
            'stored_payment_updated_at' => '2023-06-01T00:00:00+00:00',
            'authorization_id' => 'auth-1',
            'cardholder_name' => 'Test User',
            'payment_type' => 'card',
            'authentication_result' => new Model\AuthenticationResult([
                'created_at' => self::CREATED_AT,
                'eci' => '05',
                'liability_shift' => true,
                'cavv' => 'cavv-1',
                'trans_status' => 'Y',
                'three_d_challenge' => true,
                'TRA_exemption' => false,
                'trans_status_reason' => '01',
            ]),
        ]);
    }

    public static function paypalPayment(): Model\PaymentDetails {
        return new Model\PaymentDetails([
            'payer_email' => 'payer@example.com',
            'payer_status' => 'verified',
            'payer_address_status' => 'confirmed',
            'protection_eligibility' => 'Eligible',
            'payment_status' => 'completed',
            'pending_reason' => 'none',
            'authorization_id' => 'paypal-auth-1',
            'payment_type' => 'paypal',
        ]);
    }

    public static function bankTransferPayment(): Model\PaymentDetails {
        return new Model\PaymentDetails([
            'account_number' => '000123456789',
            'routing_number' => '021000021',
            'payment_type' => 'bank_transfer',
            'account_identity' => new Model\AccountIdentity([
                'names' => ['Test User'],
                'addresses' => [self::address()],
                'phone_numbers' => ['+15551234567'],
                'emails' => ['buyer@example.com'],
            ]),
            'account_balance' => new Model\AccountBalance([
                'available_balance' => 2500.0,
                'service_name' => 'plaid',
                'updated_at' => self::CREATED_AT,
                'currency_code' => 'USD',
            ]),
        ]);
    }

    public static function cardPayment114(): Model\PaymentDetails {
        $payment = self::cardPayment();
        $payment->id = 'pay-1';
        $payment->mid = 'mid-123';
        $payment->acquirer_bin = '411111';
        $payment->acquirer_region = 'EU';
        $payment->authorization_type = 'authorization';
        $payment->billing_address_id = 'addr-1';
        $payment->credit_card_country = 'US';
        $payment->expiry_month = 12;
        $payment->expiry_year = 2030;
        return $payment;
    }

    public static function digitalWalletPayment(): Model\PaymentDetails {
        return new Model\PaymentDetails([
            'payment_type' => 'apple_pay',
            'authorization_id' => 'wallet-auth-1',
            'avs_result_code' => 'Y',
            'credit_card_company' => 'visa',
            'credit_card_token' => 'wallet-token-1',
            'authorization_type' => 'verification',
            'acquirer_region' => 'NONEU',
        ]);
    }

    public static function bnplPayment(): Model\PaymentDetails {
        return new Model\PaymentDetails([
            'payment_type' => 'bnpl',
            'installments' => 4,
            'payment_frequency' => 14,
            'initial_payment_amount' => 30.125,
        ]);
    }

    public static function bankTransferPayment114(): Model\PaymentDetails {
        $payment = self::bankTransferPayment();
        $payment->token = 'bank-token-1';
        return $payment;
    }

    public static function physicalLineItem(): Model\LineItem {
        return new Model\LineItem([
            'price' => 100.0,
            'quantity' => 1,
            'title' => 'Sneakers',
            'sku' => 'SKU-1',
            'product_id' => 'prod-1',
            'fulfillment_service' => 'manual',
            'fulfillment_status' => 'fulfilled',
            'grams' => 500.0,
            'id' => 'li-1',
            'variant_id' => 'var-1',
            'variant_title' => 'Size 42',
            'variant_inventory_management' => 'shopify',
            'vendor' => 'Example Brand',
            'name' => 'Sneakers - Size 42',
            'requires_shipping' => true,
            'taxable' => true,
            'product_exists' => true,
            'condition' => 'new',
            'product_type' => 'physical',
            'category' => 'shoes',
            'brand' => 'Example Brand',
            'policy' => new Model\Policy(['evaluate' => true]),
            'delivered_at' => '2024-01-05T00:00:00+00:00',
            'release_date' => '2023-12-01',
            'size' => '42',
            'registry_type' => 'other',
            'shipping_address_id' => 'addr-1',
            'delivered_to' => 'shipping_address',
            'properties' => [new Model\Attribute(['name' => 'color', 'value' => 'red'])],
            'tax_lines' => [self::taxLine()],
            'seller' => new Model\Seller([
                'customer' => self::customer(),
                'correspondence' => 2,
                'price_negotiated' => true,
                'starting_price' => 120.0,
            ]),
        ]);
    }

    public static function digitalLineItem(): Model\LineItem {
        return new Model\LineItem([
            'price' => 50.0,
            'quantity' => 1,
            'title' => 'Gift Card',
            'product_type' => 'digital',
            'product_id' => 'prod-digital',
            'category' => 'gift_cards',
            'requires_shipping' => false,
            'photo_url' => 'https://img.example.com/card.png',
            'photo_uploaded' => true,
            'greeting_photo_url' => 'https://img.example.com/greeting.png',
            'display_name' => 'Happy Birthday',
            'message' => 'Enjoy!',
            'greeting_message' => 'Happy birthday!',
            'card_type' => 'birthday',
            'card_subtype' => 'kids',
            'sender_name' => 'Test User',
            'sender_email' => 'buyer@example.com',
            'recipient' => new Model\Recipient([
                'email' => 'friend@example.com',
                'phone' => '+15559876543',
                'social' => self::socialDetails(),
            ]),
        ]);
    }

    public static function travelLineItem(): Model\LineItem {
        return new Model\LineItem([
            'price' => 400.0,
            'quantity' => 1,
            'title' => 'JFK - LHR',
            'product_type' => 'travel',
            'product_id' => 'prod-travel',
            'category' => 'flights',
            'requires_shipping' => false,
            'leg_id' => 'leg-1',
            'departure_port_code' => 'JFK',
            'departure_city' => 'New York',
            'departure_country_code' => 'US',
            'arrival_port_code' => 'LHR',
            'arrival_city' => 'London',
            'arrival_country_code' => 'GB',
            'departure_date' => '2024-03-01T18:00:00+00:00',
            'arrival_date' => '2024-03-02T06:00:00+00:00',
            'carrier_name' => 'Example Air',
            'carrier_code' => 'EA',
            'route_index' => 1,
            'leg_index' => 1,
            'ticket_class' => 'economy',
            'transport_method' => 'plane',
        ]);
    }

    public static function passenger(): Model\Passenger {
        return new Model\Passenger([
            'first_name' => 'Test',
            'last_name' => 'User',
            'date_of_birth' => '1990-05-01',
            'nationality_code' => 'US',
            'document_number' => 'P1234567',
            'document_type' => 'passport',
            'insurance_type' => 'full',
            'insurance_price' => 25.0,
            'document_issue_date' => '2020-01-01',
            'document_expiration_date' => '2030-01-01',
            'passenger_type' => 'adult',
        ]);
    }

    public static function rideLineItem(): Model\LineItem {
        return new Model\LineItem([
            'price' => 35.0,
            'quantity' => 1,
            'title' => 'Airport transfer',
            'product_type' => 'ride',
            'product_id' => 'prod-ride',
            'category' => 'airport_transfer',
            'requires_shipping' => false,
            'pickup_date' => '2024-03-01T15:00:00+00:00',
            'pickup_latitude' => 40.7128,
            'pickup_longitude' => -74.006,
            'pickup_address' => self::address(),
            'dropoff_date' => '2024-03-01T16:00:00+00:00',
            'dropoff_latitude' => 40.6413,
            'dropoff_longitude' => -73.7781,
            'dropoff_address' => self::address(),
            'price_by' => 'distance',
            'vehicle_class' => 'sedan',
            'driver_id' => 'driver-1',
            'tariff' => 'standard',
            'note_to_driver' => 'Call on arrival',
            'meet_n_greet' => 'yes',
            'authorized_payments' => 40.0,
            'route_index' => 1,
            'leg_index' => 1,
        ]);
    }

    public static function eventLineItem(): Model\LineItem {
        return new Model\LineItem([
            'price' => 80.0,
            'quantity' => 2,
            'title' => 'Concert ticket',
            'product_type' => 'event',
            'product_id' => 'prod-event',
            'requires_shipping' => false,
            'category' => 'music',
            'sub_category' => 'rock',
            'section' => 'A1',
            'event_date' => '2024-04-01T20:00:00+00:00',
            'city' => 'New York',
            'country_code' => 'US',
            'latitude' => '40.7505',
            'longitude' => '-73.9934',
        ]);
    }

    public static function accommodationLineItem(): Model\LineItem {
        return new Model\LineItem([
            'price' => 300.0,
            'quantity' => 1,
            'title' => 'Hotel stay',
            'product_type' => 'accommodation',
            'product_id' => 'prod-accommodation',
            'category' => 'hotels',
            'requires_shipping' => false,
            'room_type' => 'double',
            'city' => 'London',
            'country_code' => 'GB',
            'check_in_date' => '2024-03-02T15:00:00+00:00',
            'check_out_date' => '2024-03-05T11:00:00+00:00',
            'rating' => 4.5,
            'number_of_guests' => 2,
            'cancellation_policy' => 'free_cancellation',
            'accommodation_type' => 'hotel',
        ]);
    }
}
