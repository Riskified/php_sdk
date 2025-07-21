<?php
declare(strict_types=1);

/**
 * Riskified Checkout Submit Example
 * 
 * This example demonstrates how to submit a checkout using the Riskified PHP SDK.
 * Usage: php sample/order_advise_submit.php
 */

include __DIR__ . '/../src/Riskified/autoloader.php';

use Riskified\Common\Riskified;
use Riskified\Common\Env;
use Riskified\Common\Validations;
use Riskified\Common\Signature;
use Riskified\OrderWebhook\Model;
use Riskified\OrderWebhook\Transport;

// Replace with the 'shop domain' of your account in Riskified
$domain = "[your shop domain as registered to Riskified]";

// Replace with the 'auth token' listed in the Riskified web app under the 'Settings' Tab
$authToken = "[your authentication token string]";

// Initialize Riskified SDK
Riskified::init($domain, $authToken, Env::SANDBOX, Validations::IGNORE_MISSING);

// Create Checkout
$checkout = new Model\Checkout([
    'id' => '1234phpsdksimple',
    'email' => 'great.customer@example.com',
    'created_at' => '2018-08-22T11:00:00-05:00',
    'currency' => 'USD',
    'updated_at' => '2018-08-22T11:00:00-05:00',
    'gateway' => 'mypaymentprocessor',
    'browser_ip' => '124.185.86.55',
    'total_price' => 113.23,
    'total_discounts' => 5.0,
    'cart_token' => '1sdaf23j212',
    'device_id' => '01234567-89ABCDEF-01234567-89ABCDEF',
    'note' => 'Shipped to my hotel.',
    'referring_site' => 'google.com',
    'source' => 'desktop_web'
]);

// Create Line Items
$lineItem1 = new Model\LineItem([
    'price' => 100,
    'quantity' => 1,
    'title' => 'ACME Widget',
    'product_id' => '101',
    'sku' => 'ABCD',
    'registry_type' => 'baby'
]);

$lineItem2 = new Model\LineItem([
    'price' => 200,
    'quantity' => 4,
    'title' => 'ACME Spring',
    'product_id' => '202',
    'sku' => 'EFGH',
    'registry_type' => 'wedding'
]);

$lineItem3 = new Model\LineItem([
    'price' => 150,
    'quantity' => 2,
    'title' => "New York Yankees game",
    'section' => 'Bleachers',
    'city' => 'New York City',
    'event_date' => '2019-07-12T19:00:00-4:00',
    'country_code' => 'US',
    'latitude' => '40.8296 N',
    'longitude' => '73.9262 W',
    'product_type' => 'event'
]);

$checkout->line_items = [$lineItem1, $lineItem2, $lineItem3];

// Create Discount Codes
$discountCode = new Model\DiscountCode([
    'amount' => 19.95,
    'code' => '12'
]);
$checkout->discount_codes = [$discountCode];

// Create Shipping Lines
$shippingLine = new Model\ShippingLine([
    'price' => 123.00,
    'title' => 'Free Shipping'
]);
$checkout->shipping_lines = [$shippingLine];

// Create Payment Details
$paymentDetails = new Model\PaymentDetails([
    'credit_card_bin' => '370002',
    'avs_result_code' => 'Y',
    'cvv_result_code' => 'N',
    'credit_card_number' => 'xxxx-xxxx-xxxx-1234',
    'credit_card_company' => 'VISA'
]);
$checkout->payment_details = [$paymentDetails];

// Create Customer
$customer = new Model\Customer([
    'email' => 'email@address.com',
    'first_name' => 'Firstname',
    'last_name' => 'Lastname',
    'id' => '1233',
    'created_at' => '2008-01-10T11:00:00-05:00',
    'orders_count' => 6,
    'verified_email' => true,
    'account_type' => 'free'
]);
$checkout->customer = $customer;

// Create Billing Address
$billingAddress = new Model\Address([
    'first_name' => 'John',
    'last_name' => 'Doe',
    'address1' => '108 Main Street',
    'company' => 'Kansas Computers',
    'country' => 'United States',
    'country_code' => 'US',
    'phone' => '1234567',
    'city' => 'NYC',
    'name' => 'John Doe',
    'address2' => 'Apartment 12',
    'province' => 'New York',
    'province_code' => 'NY',
    'zip' => '64155'
]);
$checkout->billing_address = $billingAddress;

// Create Shipping Address
$shippingAddress = new Model\Address([
    'first_name' => 'John',
    'last_name' => 'Doe',
    'address1' => '108 Main Street',
    'company' => 'Kansas Computers',
    'country' => 'United States',
    'country_code' => 'US',
    'phone' => '1234567',
    'city' => 'NYC',
    'name' => 'John Doe',
    'address2' => 'Apartment 12',
    'province' => 'New York',
    'province_code' => 'NY',
    'zip' => '64155'
]);
$checkout->shipping_address = $shippingAddress;

// Display checkout request
echo "\nCHECKOUT REQUEST:" . PHP_EOL .$checkout->toJson() . PHP_EOL;

// Create a curl transport to the Riskified Server
$transport = new Transport\CurlTransport(new Signature\HttpDataSignature());
$transport->timeout = 10;

// Submit the checkout to Riskified
try {
    $response = $transport->advise($checkout);
    echo PHP_EOL . "Checkout submission successful. Riskified response: " . PHP_EOL . 
         json_encode($response, JSON_PRETTY_PRINT) . PHP_EOL;
} catch (\Riskified\OrderWebhook\Exception\UnsuccessfulActionException $unsuccessfulException) {
    echo PHP_EOL . "Checkout submission failed. HTTP Status: " . $unsuccessfulException->statusCode . 
         PHP_EOL . "Response body: " . json_encode($unsuccessfulException->jsonResponse, JSON_PRETTY_PRINT) . PHP_EOL;
} catch (Exception $generalException) {
    echo PHP_EOL . "Checkout submission encountered an error: " . $generalException->getMessage() . PHP_EOL;
}
