# Migrating to 2.0.0

Version 2.0.0 of the SDK will remove what 1.15.0 and 1.16.0 kept only for backward compatibility. This
guide lists every deprecated item, what to use instead, and what changes when you upgrade.

You can make all of these changes on 1.16.0, before 2.0.0 is released. If your code runs on 1.16.0
without the deprecated items below, and with `Riskified::$legacy_wire_format` left at `false`, the upgrade
to 2.0.0 shouldn't change what your integration sends.

## Before you upgrade

1. Make sure `Riskified::$legacy_wire_format` is `false` (the default) and that Riskified accepts your
   orders. This is the JSON 2.0.0 sends.
2. Replace each deprecated item below with its replacement.
3. Pass the documented types for the fields in [Stricter fields](#stricter-fields).

## Removed

| Deprecated | Use instead | In 2.0.0 |
|---|---|---|
| `Riskified::$legacy_wire_format` | Nothing: remove the assignment | Removed. Models are always sent in the documented JSON form. |
| `LineItem` `authorized_payments` | `authorized_payment` | Removed. Setting it throws `InvalidPropertyException`. 1.15.0 and 1.16.0 send the value under both keys; 2.0.0 sends only `authorized_payment`. |
| `Order` / `Checkout` root `authorization_error` | `authorization_error` on each `PaymentDetails` | Removed. 1.15.0 and 1.16.0 copy the root value into each payment detail on checkouts; 2.0.0 doesn't. |

```php
// Before
$lineItem->authorized_payments = 300.0;
$checkout->authorization_error = $error;

// After
$lineItem->authorized_payment = 300.0;
$checkout->payment_details = array(new Model\PaymentDetails(array(
    // ...
    'authorization_error' => $error,
)));
```

## Single objects where the API documents a list

These fields accept a list since 1.15.0, and a single object is sent as a one-element list. Passing a single
object is deprecated; 2.0.0 is planned to accept only lists.

| Field | Pass instead |
|---|---|
| `Order` `billing_address`, `shipping_address` | `array(new Model\Address(...))` |
| `LineItem` `pickup_address`, `dropoff_address` | `array(new Model\Address(...))` |
| `Decision` `payment_details` | `array(new Model\PaymentDetails(...))` |
| `Order` `charge_free_payment_details` | `array(new Model\ChargeFreePaymentDetails(...))` |

```php
// Before
$order->billing_address = $address;

// After
$order->billing_address = array($address);
```

`Checkout` addresses are still sent as single objects, because post-auth `/checkout_denied` documents them
that way. Whether they become lists in 2.0.0 depends on an open question with the Riskified API team; this
guide will be updated when it's answered.

## Stricter fields

1.15.0 relaxed these specs to `mixed` so the SDK could convert values to the documented types. 2.0.0 is
planned to restore strict specs with those types, so values the SDK converts today will fail validation.

| Field | 1.15.0 / 1.16.0 | Pass in 2.0.0 |
|---|---|---|
| `LineItem` `latitude`, `longitude` | Any value; numeric values are sent as floats | A float, e.g. `40.7505` |
| `LineItem` `rating` | Any value; numbers are sent as strings | A string, e.g. `'4.5'` |
| `ChargeFreePaymentDetails` `amount` | Any value; numeric values are sent as floats | A float, e.g. `5.25` |
| `LineItem` `vendor` | A string or a `Vendor` | A `Model\Vendor` |
| `Order` `charge_free_payment_details` | Any value | A list of `Model\ChargeFreePaymentDetails` |

How a string `vendor` is sent until then also depends on an open question with the Riskified API team.

## Validation

Whether 2.0.0 keeps client-side validation, and in what form, hasn't been decided. Validation bugs that
are known today are left as they are until then.
