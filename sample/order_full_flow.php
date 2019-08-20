<?php
/**
 * Copyright 2013-2015 Riskified.com, Inc. or its affiliates. All Rights Reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may not use this file except in compliance with the License.
 * A copy of the License is located at
 *
 * http://www.apache.org/licenses/LICENSE-2.0.html
 *
 * or in the "license" file accompanying this file. This file is distributed
 * on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either
 * express or implied. See the License for the specific language governing
 * permissions and limitations under the License.
 */

// An example of a complete order flow.
// Usage: php order_full_flow.php

include __DIR__.'/../src/Riskified/autoloader.php';
use Riskified\Common\Riskified;
use Riskified\Common\Env;
use Riskified\Common\Validations;
use Riskified\Common\Signature;
use Riskified\OrderWebhook\Model;
use Riskified\OrderWebhook\Transport;

# Replace with the 'shop domain' of your account in Riskified
$domain = "[your shop domain as registered to Riskified]";

# Replace with the 'auth token' listed in the Riskified web app under the 'Settings' Tab
$authToken = "[your authentication token string]";

Riskified::init($domain, $authToken, Env::SANDBOX, Validations::ALL);

$order_details = array(
    'id' => 'ch567phpsdkfullflow0',
    'name' => '#1234',
    'email' => 'erin.o\'neill@cbre.com',
    'created_at' => '2018-08-23T11:00:00-05:00',
    'closed_at' => null,
    'currency' => 'CAD',
    'updated_at' => '2018-08-23T11:00:00-05:00',
    'gateway' => 'mypaymentprocessor',
    'browser_ip' => '124.185.86.55',
    'total_price' => 113.23,
    'total_discounts' => 5.0,
    'cart_token' => '1sdaf23j212',
    'additional_emails' => array('my@email.com', 'second@email.co.uk', 'third2@email.rr.com'),
    'note' => 'Shipped to my hotel.',
    'referring_site' => 'google.com',
    'source' => 'desktop_web',
    'line_items' => array(
        new Model\LineItem(array(
            'recipient' => new Model\Recipient(array(
                'email' => '1@gmail.com'
            )),
            'price' => 100,
            'quantity' => 1,
            'title' => 'ACME Widget',
            'product_id' => '101',
            'sku' => 'ABCD',
            'delivered_to' => 'store_pickup',
            'size' => '13',
            'release_date' => '2016-03-10T11:00:00-05:00',
            'seller' => new Model\Seller(array(
                'customer' => new Model\Customer(array(
                    'email' => 'email@address.com',
                    'first_name' => 'Firstname',
                    'last_name' => 'Lastname',
                    'id' => '1233',
                    'created_at' => '2008-01-10T11:00:00-05:00',
                    'orders_count' => 6,
                    'verified_email' => true,
                    'account_type' => 'free',
                    'buy_attempts' => 3,
                    'sell_attempts' => 44
                ))
        ))),
        // Digital Goods product example using "requires_shipping":false
        new Model\LineItem(array(
            'title' => 'Giftcard',
            'price' => 100,
            'quantity' => 1,
            'requires_shipping' => false,
            'delivered_at' => '2017-03-10T11:00:00-05:00',
            'sender_name' => 'John Doe',
            'sender_email' => 'email@address.com'
        )),
        new Model\LineItem(array(
            'price' => 200,
            'quantity' => 4,
            'title' => 'ACME Spring',
            'product_id' => '202',
            'sku' => 'EFGH',
            'category' => 'ACME Spring Category',
            'sub_category' => 'ACME Spring Sub Category'
        ))
    )),
    'discount_codes' =>  array(
        new Model\DiscountCode(array(
            'amount' => 19.95,
            'code' => '12'
        ))
    ),
    'shipping_lines' => array(
        new Model\ShippingLine(array(
            'title' => 'FedEx',
            'price' => 123.00,
            'code' => 'Free'
        ))
    ),
    'payment_details' => array(new Model\PaymentDetails(array(
        'credit_card_bin' => '370002',
        'credit_card_number' => 'xxxx-xxxx-xxxx-1234',
        'credit_card_company' => 'VISA',
        'credit_card_token' => '0022334466',
        '_type' => 'credit_card',

### required for checkout denied: ###
#        'authorization_error' => new Model\AuthorizationError(array(
#                                                                      'created_at' => '2008-01-10T11:00:00-05:00',
#                                                                      'error_code' => 'card_rejected'
#                                                                  ))
    ))),
    'customer' => new Model\Customer(array(
        'email' => 'email@address.com',
        'first_name' => 'Firstname',
        'last_name' => 'Lastname',
        'id' => '1233',
        'created_at' => '2016-12-11T11:00:00-05:00',
        'orders_count' => 6,
        'verified_email' => true,
        'account_type' => 'free',
        'buy_attempts' => 5,
        'sell_attempts' => 7
    )),
    'billing_address' => new Model\Address(array(
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
    )),
    'shipping_address' => new Model\Address(array(
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
    )),
    'passengers' => array(
        new Model\Passenger(array(
            'first_name' => 'John',
            'last_name' => 'Doe',
            'date_of_birth' => '1980-01-10',
            'nationality_code' => 'US',
            'document_number' => '123abc',
            'document_type' => 'passport'
        ))
    ),
    'charge_free_payment_details' => new Model\ChargeFreePaymentDetails(array(
        'gateway' => 'giftcard',
        'amount' => '50'
    ))
);


# Create a curl transport to the Riskified Server
$transport = new Transport\CurlTransport(new Signature\HttpDataSignature());
$transport->timeout = 10;


#### Create Checkout
$checkout = new Model\Checkout($order_details);

$response = $transport->createCheckout($checkout);
echo PHP_EOL."Create Checkout succeeded. Response: ".PHP_EOL.json_encode($response).PHP_EOL;

#### Advise Checkout (uncomment if eligible for /advise)
//$checkout = new Model\Checkout($order_details);
//
//$response = $transport->adviseOrder($checkout);
//echo PHP_EOL."Advise Checkout succeeded. Response: ".PHP_EOL.json_encode($response).PHP_EOL;

#### Notify Checkout Denied
$response = $transport->deniedCheckout($checkout);
echo PHP_EOL."Denied Checkout succeeded. Response: ".PHP_EOL.json_encode($response).PHP_EOL;


#### Create and Submit Order
$order = new Model\Order($order_details);
$order->checkout_id = $order->id;
$order->id = 'or1234';
$order->payment_details[0]->avs_result_code = 'Y';
$order->payment_details[0]->cvv_result_code = 'N';

##REQUIRED FOR PSD2 ORDERS##
//$authenticationResult = new Model\AuthenticationResult(array(
//    'created_at' => '2019-07-17T15:00:00-05:00',
//    'eci' => '07',
//    'cavv' => '05',
//    'trans_status' => 'Y',
//    'trans_status_reason' => '01'
//));
//$order->payment_details[0]->$authenticationResult;

$response = $transport->createOrder($order);
echo PHP_EOL."Create Order succeeded. Response: ".PHP_EOL.json_encode($response).PHP_EOL;

$response = $transport->submitOrder($order);
echo PHP_EOL."Submit Order succeeded. Response: ".PHP_EOL.json_encode($response).PHP_EOL;


#### Update Order
$updatedOrder = new Model\Order(array(
    'id' => $order->id,
    'email' => 'another.email@example.com'
));

$response = $transport->updateOrder($updatedOrder);
echo PHP_EOL."Update Order succeeded. Response: ".PHP_EOL.json_encode($response).PHP_EOL;


#### Decide Order
$response = $transport->decideOrder($order);
echo PHP_EOL."Decide Order succeeded. Response: ".PHP_EOL.json_encode($response).PHP_EOL;

#### Partially Refund Order
$refund = new Model\Refund(array(
    'id' => $order->id,
    'refunds' => array(new Model\RefundDetails(array(
        'refund_id' => 'refund_001',
        'amount' => 33.12,
        'currency' => 'USD',
        'reason' => 'Product Missing'
    )))
));
$response = $transport->refundOrder($refund);
echo PHP_EOL."Refund Order succeeded. Response: ".PHP_EOL.json_encode($response).PHP_EOL;


#### Notify Order Fulfillment
$fullfillments = new Model\Fulfillment(array (
    'id' => $order->id,
    'fulfillments' => array(new Model\FulfillmentDetails(array(
        'fulfillment_id' => '123',
        'created_at' =>  '2013-04-23T13:36:50Z',
        'status' => 'success',
        'tracking_company' =>  'fedex',
        'tracking_numbers' => 'abc123',
        'tracking_urls' => 'http://fedex.com/track?q=abc123',
        'message' => 'estimated delivery 2 days',
        'receipt' => 'authorization: 765656'
    )))
));

$response = $transport->fulfillOrder($fullfillments);
echo PHP_EOL."Fulfill Order succeeded. Response: ".PHP_EOL.json_encode($response).PHP_EOL;


#### Cancel (Full Refund) Order
$response = $transport->cancelOrder($order);
echo PHP_EOL."Cancel Order succeeded. Response: ".PHP_EOL.json_encode($response).PHP_EOL;

#### Check if order is eligible for Deco Payment and opt-in to Deco payment
$checkout = new Model\Checkout($order_details);
$response = $transport->createCheckout($checkout);
echo PHP_EOL."Create Checkout succeeded. Response: ".PHP_EOL.json_encode($response).PHP_EOL;

$order = new Model\Order(array(
    'id' => $checkout->id
));

$response = $transport->eligible($order);
echo PHP_EOL."Eligible. Response: ".PHP_EOL.json_encode($response).PHP_EOL;

$response = $transport->opt_in($order);
echo PHP_EOL."Opt-in. Response: ".PHP_EOL.json_encode($response).PHP_EOL;

#### Login Account Action
$login = new Model\Login(array(
    'customer_id' => '207119551',
    'login_status' => new Model\LoginStatus(array(
        'login_status_type' => 'success'
    )),
    'client_details' => new Model\ClientDetails(array(
        'user_agent' => 'Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; Trident/5.0)'
    )),
    'session_details' => new Model\SessionDetails(array(
        'created_at' => '2018-06-13T14:31-04:00',
        'cart_token' => '68778783ad298f1c80c3bafcddeea02f',
        'browser_ip' => '111.111.111.111',
        'source' => 'desktop_web'
    ))
));

$response = $transport->login($login);
echo PHP_EOL."Login event succeeded. Response: ".PHP_EOL.json_encode($response).PHP_EOL;

#### CustomerCreate account action
$customer_create = new Model\CustomerCreate(array(
    'customer_id' => '207119551',
    'phone_mandatory' => false,
    'client_details' => new Model\ClientDetails(array(
        'accept_language' => 'en_CA',
        'user_agent' => 'Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; Trident/5.0)'
    )),
    'session_details' => new Model\SessionDetails(array(
        'created_at' => '2018-06-13T14:31-04:00',
        'cart_token' => '68778783ad298f1c80c3bafcddeea02f',
        'browser_ip' => '111.111.111.111',
        'source' => 'desktop_web'
    )),
    'customer' => new Model\Customer(array(
        'email' => 'bob.norman@hostmail.com',
        'verified_email' => true,
        'first_name' => 'Bob',
        'last_name' => 'Norman',
        'id' => '207119551',
        'created_at' => '2013-04-23T13:36:50-04:00'
    )),
    'payment_details' => array(
        new Model\PaymentDetails(array(
            'avs_result_code' => 'Y',
            'credit_card_bin' => '123456',
            'credit_card_company' => 'Visa',
            'credit_card_number' => 'XXXX-XXXX-XXXX-4242',
            'cvv_result_code' => 'M',
            'authorization_id' => 'd3j555kdjgnngkkf3_1',
            'authorization_error' => new Model\AuthorizationError(array(
                'created_at' => '2008-01-10T11:00:00-05:00',
                'error_code' => 'card_declined',
                'message' => 'insufficient funds'
            ))
        ))
    ),
    'billing_address' => array(
        new Model\Address(array(
            'address1' => 'Chestnut Street 92',
            'address2' => '',
            'city' => 'Louisville',
            'company' => null,
            'country' => 'United States',
            'country_code' => 'US',
            'first_name' => 'Bob',
            'last_name' => 'Norman',
            'phone' => '555-625-1199',
            'province' => 'Kentucky',
            'province' => 'KY',
            'zip' => '40202'
        ))
    ),
    'shipping_address' => array(
        new Model\Address(array(
            'address1' => 'Chestnut Street 92',
            'address2' => '',
            'city' => 'Louisville',
            'company' => null,
            'country' => 'United States',
            'country_code' => 'US',
            'first_name' => 'Bob',
            'last_name' => 'Norman',
            'phone' => '555-625-1199',
            'province' => 'Kentucky',
            'province' => 'KY',
            'zip' => '40202'
        ))
    )
));

$response = $transport->customerCreate($customer_create);
echo PHP_EOL."Customer Create event succeeded. Response: ".PHP_EOL.json_encode($response).PHP_EOL;

#### CustomerUpdate account action
$customer_update = new Model\CustomerUpdate(array(
    'customer_id' => '207119551',
    'password_changed' => false,
    'client_details' => new Model\ClientDetails(array(
        'accept_language' => 'en_CA',
        'user_agent' => 'Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; Trident/5.0)'
    )),
    'session_details' => new Model\SessionDetails(array(
        'created_at' => '2018-06-13T14:31-04:00',
        'cart_token' => '68778783ad298f1c80c3bafcddeea02f',
        'browser_ip' => '111.111.111.111',
        'source' => 'desktop_web'
    )),
    'customer' => new Model\Customer(array(
        'email' => 'bob.norman@hostmail.com',
        'verified_email' => true,
        'first_name' => 'Bob',
        'last_name' => 'Norman',
        'id' => '207119551',
        'created_at' => '2013-04-23T13:36:50-04:00'
    )),
    'payment_details' => array(
        new Model\PaymentDetails(array(
            'avs_result_code' => 'Y',
            'credit_card_bin' => '123456',
            'credit_card_company' => 'Visa',
            'credit_card_number' => 'XXXX-XXXX-XXXX-4242',
            'cvv_result_code' => 'M',
            'authorization_id' => 'd3j555kdjgnngkkf3_1',
            'authorization_error' => new Model\AuthorizationError(array(
                'created_at' => '2008-01-10T11:00:00-05:00',
                'error_code' => 'card_declined',
                'message' => 'insufficient funds'
            ))
        ))
    ),
    'billing_address' => array(
        new Model\Address(array(
            'address1' => 'Chestnut Street 92',
            'address2' => '',
            'city' => 'Louisville',
            'company' => null,
            'country' => 'United States',
            'country_code' => 'US',
            'first_name' => 'Bob',
            'last_name' => 'Norman',
            'phone' => '555-625-1199',
            'province' => 'Kentucky',
            'province' => 'KY',
            'zip' => '40202'
        ))
    ),
    'shipping_address' => array(
        new Model\Address(array(
            'address1' => 'Chestnut Street 92',
            'address2' => '',
            'city' => 'Louisville',
            'company' => null,
            'country' => 'United States',
            'country_code' => 'US',
            'first_name' => 'Bob',
            'last_name' => 'Norman',
            'phone' => '555-625-1199',
            'province' => 'Kentucky',
            'province' => 'KY',
            'zip' => '40202'
        ))
    )
));

$response = $transport->customerUpdate($customer_update);
echo PHP_EOL."Customer Update event succeeded. Response: ".PHP_EOL.json_encode($response).PHP_EOL;

#### Logout account action

$logout = new Model\Logout(array(
    'customer_id' => '207119551',
    'client_details' => new Model\ClientDetails(array(
        'accept_language' => 'en_CA',
        'user_agent' => 'Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; Trident/5.0)'
    )),
    'session_details' => new Model\SessionDetails(array(
        'created_at' => '2018-06-13T14:31-04:00',
        'cart_token' => '68778783ad298f1c80c3bafcddeea02f',
        'browser_ip' => '111.111.111.111',
        'source' => 'desktop_web'
    ))
));

$response = $transport->logout($logout);
echo PHP_EOL."Logout event succeeded. Response: ".PHP_EOL.json_encode($response).PHP_EOL;

#### ResetPasswordRequest account action

$resetPasswordRequest = new Model\ResetPasswordRequest(array(
    'customer_id' => '207119551',
    'client_details' => new Model\ClientDetails(array(
        'accept_language' => 'en_CA',
        'user_agent' => 'Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; Trident/5.0)'
    )),
    'session_details' => new Model\SessionDetails(array(
        'created_at' => '2018-06-13T14:31-04:00',
        'cart_token' => '68778783ad298f1c80c3bafcddeea02f',
        'browser_ip' => '111.111.111.111',
        'source' => 'desktop_web'
    ))
));

$response = $transport->resetPasswordRequest($resetPasswordRequest);
echo PHP_EOL."ResetPasswordRequest event succeeded. Response: ".PHP_EOL.json_encode($response).PHP_EOL;

#### WishlistChanges account action

$wishlist_changes = new Model\WishlistChanges(array(
    'customer_id' => '207119551',
    'wishlist_action' => 'add',
    'client_details' => new Model\ClientDetails(array(
        'accept_language' => 'en_CA',
        'user_agent' => 'Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; Trident/5.0)'
    )),
    'session_details' => new Model\SessionDetails(array(
        'created_at' => '2018-06-13T14:31-04:00',
        'cart_token' => '68778783ad298f1c80c3bafcddeea02f',
        'browser_ip' => '111.111.111.111',
        'source' => 'desktop_web'
    )),
    'line_item' => new Model\LineItem(array(
        'price' => 199,
        'quantity' => 1,
        'title' => 'IPod Nano - 8gb - green',
        'product_id' => '632910392',
        'brand' => 'Apple',
        'product_type' => 'physical'
    ))
));

$response = $transport->wishlistChanges($wishlist_changes);
echo PHP_EOL."WishlistChanges event succeeded. Response: ".PHP_EOL.json_encode($response).PHP_EOL;

#### Redeem account action

$redeem = new Model\Redeem(array(
    'customer_id' => '207119551',
    'redeem_type' => 'promo code',
    'client_details' => new Model\ClientDetails(array(
        'accept_language' => 'en_CA',
        'user_agent' => 'Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; Trident/5.0)'
    )),
    'session_details' => new Model\SessionDetails(array(
        'created_at' => '2018-06-13T14:31-04:00',
        'cart_token' => '68778783ad298f1c80c3bafcddeea02f',
        'browser_ip' => '111.111.111.111',
        'source' => 'desktop_web'
    ))
));

$response = $transport->redeem($redeem);
echo PHP_EOL."Redeem event succeeded. Response: ".PHP_EOL.json_encode($response).PHP_EOL;

#### CustomerReachOut account action

$customer_reach_out = new Model\CustomerReachOut(array(
    'customer_id' => '207119551',
    'order_id' => '450789469',
    'contact_method' => new Model\ContactMethod(array(
        'contact_method_type' => 'email',
        'email' => 'moo@gmail.com'
    )),
    'client_details' => new Model\ClientDetails(array(
        'accept_language' => 'en_CA',
        'user_agent' => 'Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; Trident/5.0)'
    )),
    'session_details' => new Model\SessionDetails(array(
        'created_at' => '2018-06-13T14:31-04:00',
        'cart_token' => '68778783ad298f1c80c3bafcddeea02f',
        'browser_ip' => '111.111.111.111',
        'source' => 'desktop_web'
    ))
));

$response = $transport->customerReachOut($customer_reach_out);
echo PHP_EOL."CustomerReachOut event succeeded. Response: ".PHP_EOL.json_encode($response).PHP_EOL;
