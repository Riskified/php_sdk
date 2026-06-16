# Riskified PHP SDK

A PHP client for the [Riskified](https://www.riskified.com) API. It lets you send orders, checkouts and
account/lifecycle events to Riskified for fraud and chargeback protection, and verify the decision
notifications Riskified sends back to your application.

- **Current version:** 1.12.0
- **API version:** 2

For full API details, see the [Riskified API reference](https://apiref.riskified.com).

## Requirements

- PHP >= 7.0
- The `curl` and `json` PHP extensions

## Installation

Install via [Composer](https://getcomposer.org):

```bash
composer require riskified/php_sdk
```

## Getting started

Initialize the SDK once with your shop domain and authentication token (both available in the Riskified
web app under **Settings**), then build and submit an order.

```php
use Riskified\Common\Riskified;
use Riskified\Common\Env;
use Riskified\Common\Validations;
use Riskified\Common\Signature;
use Riskified\OrderWebhook\Model;
use Riskified\OrderWebhook\Transport;

Riskified::init(
    'your-shop-domain.com',          // shop domain registered with Riskified
    'your-auth-token',               // auth token from the Riskified web app
    Env::SANDBOX,                    // Env::SANDBOX | Env::PROD | Env::DEV
    Validations::IGNORE_MISSING      // validation mode
);

$order = new Model\Order(array(
    'id'          => '1234',
    'email'       => 'great.customer@example.com',
    'created_at'  => '2026-06-16T11:00:00-05:00',
    'currency'    => 'USD',
    'total_price' => 113.23,
    'browser_ip'  => '124.185.86.55',
));

// ...attach line items, addresses, payment details, customer, etc.

$transport = new Transport\CurlTransport(new Signature\HttpDataSignature());
$transport->timeout = 10;

try {
    $response = $transport->submitOrder($order);
    echo "Order submitted. Response: " . json_encode($response) . PHP_EOL;
} catch (\Riskified\OrderWebhook\Exception\UnsuccessfulActionException $e) {
    echo "Failed with status {$e->statusCode}: " . json_encode($e->jsonResponse) . PHP_EOL;
} catch (\Exception $e) {
    echo "Failed: " . $e->getMessage() . PHP_EOL;
}
```

### Environments

`Riskified::init()` accepts an environment as its third argument:

| Constant       | Target                          |
| -------------- | ------------------------------- |
| `Env::SANDBOX` | Riskified sandbox (default)     |
| `Env::PROD`    | Riskified production            |
| `Env::DEV`     | Local development (`localhost`) |

### Validation modes

The fourth argument controls how strictly the SDK validates models before sending them:

| Constant                       | Behavior                                        |
| ------------------------------ | ----------------------------------------------- |
| `Validations::SKIP`            | No client-side validation                       |
| `Validations::IGNORE_MISSING`  | Validate present fields, ignore missing (default) |
| `Validations::ALL`             | Require all mandatory fields                    |

## Available operations

The `Transport\CurlTransport` exposes a method per Riskified API endpoint, including:

- **Orders:** `createOrder`, `updateOrder`, `submitOrder`, `cancelOrder`, `refundOrder`,
  `fulfillOrder`, `decideOrder`, `chargebackOrder`, `sendHistoricalOrders`
- **Checkout:** `createCheckout`, `deniedCheckout`, `advise`, `checkout_decide`
- **Account & lifecycle:** `login`, `logout`, `customerCreate`, `customerUpdate`,
  `verification`, `wishlistChanges`, `redeem`, `eligible`, `opt_in`

## Decision notifications

Riskified sends decision notifications to a callback endpoint you configure. Use the
`DecisionNotification\Model\Notification` class to verify the request signature and parse the payload.
Pass an associative array of the request's HTTP headers (matching the format returned by
[`getallheaders()`](https://www.php.net/manual/en/function.getallheaders.php)):

```php
use Riskified\Common\Riskified;
use Riskified\Common\Signature;
use Riskified\DecisionNotification\Model;

Riskified::init('your-shop-domain.com', 'your-auth-token');

$signature = new Signature\HttpDataSignature();
$headers   = getallheaders();
$body      = file_get_contents('php://input');

$notification = new Model\Notification($signature, $headers, $body);

echo "Order #{$notification->id} -> {$notification->status}: {$notification->description}";
```

## Development

Install dependencies and run the tooling via Composer:

```bash
composer install

composer lint           # check coding standards (PHP_CodeSniffer)
composer fix            # auto-fix coding standards (PHPCBF)
composer analyse        # run static analysis (PHPStan)
composer check          # lint + analyse

vendor/bin/phpunit      # run the test suite
```

## Migrating to API Version 2

API Version 2 introduces new features (and breaks some old ones).

### Order Webhook

This version represents a shift from data-driven order handling to multiple API endpoints, each designed
for a specific purpose. These include:

* `/api/create` - served by `$transport->createOrder()`
* `/api/update` - served by `$transport->updateOrder()`
* `/api/submit` - served by `$transport->submitOrder()`
* `/api/refund` - served by `$transport->refundOrder()`
* `/api/cancel` - served by `$transport->cancelOrder()`

Refer to the online [documentation](https://apiref.riskified.com) for more details.
When migrating from version 1, you'll need to separate the different calls to Riskified's API to support this new process.

### Decision Notifications

#### Constructor `$headers` argument format

The format of the `$headers` argument when constructing a new `Riskified\DecisionNotification\Notification` instance has changed.
The constructor now expects an associative array of all the HTTP headers of the request, and *not* a flat array of strings, as
in previous versions of this SDK.

This change should simplify integration since the argument now follows the format of the return value of the popular PHP/Apache
function [`getallheaders()`](https://www.php.net/manual/en/function.getallheaders.php).

#### API v2 payload format

Notification requests in API version 2 now contain a JSON encoded payload which is more flexible and easily extended.

If you are already using the `Notification` class in version 1, there are no additional actions required to support the
migration to JSON, as this SDK handles the new data format seamlessly.
