<?php namespace Riskified\OrderWebhook\Model;
/**
 * Copyright 2013-2014 Riskified.com, Inc. or its affiliates. All Rights Reserved.
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

/**
 * Class Order
 * main data model, includes nested models
 * @package Riskified\OrderWebhook\Model
 */
class Order extends AbstractModel {

    protected $_fields = array(
        'id' => 'string',
        'name' => 'string optional',
        'email' => 'string /^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i',
        'total_spent' => 'float optional',
        'cancel_reason' => 'string optional',
        'cancelled_at' => 'date optional',
        'created_at' => 'date',
        'closed_at' => 'date optional',
        'currency' => 'string',
        'updated_at' => 'date',
        'gateway' => 'string',
        'browser_ip' => 'string /^(\d{1,3}\.){3}\d{1,3}$/',
        'cart_token' => 'string optional',
        'note' => 'string optional',
        'referring_site' => 'string optional',
        'total_price' => 'float',
        'total_discounts' => 'float optional',
        'customer' => 'object \Customer',
        'shipping_address' => 'object \Address',
        'billing_address' => 'object \Address',
        'payment_details' => 'object \PaymentDetails optional',
        'line_items' => 'objects \LineItem',
        'discount_codes' => 'objects \DiscountCode optional',
        'shipping_lines' => 'objects \ShippingLine optional'
    );
}
