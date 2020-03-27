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

// Examples of all account actions.
// Usage: php account_action_request.php

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

# Create a curl transport to the Riskified Server
$transport = new Transport\CurlTransport(new Signature\HttpDataSignature());
$transport->timeout = 10;

#### Login Account Action
$login = new Model\Login(array(
    'customer_id' => '207119551',
    'email' => 'bob.norman@hostmail.com',
    'social_login_type' => 'amazon',
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
        'product_type' => 'physical',
        'category' => 'electronics'
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
