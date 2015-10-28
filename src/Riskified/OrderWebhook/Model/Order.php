<?php namespace Riskified\OrderWebhook\Model;
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

/**
 * Class Order
 * main data model, includes nested models
 * @package Riskified\OrderWebhook\Model
 */
class Order extends AbstractModel {

    protected $_fields = array(
        'id' => 'string',
        'email' => 'string /^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,20}$/i',
        'created_at' => 'date',
        'updated_at' => 'date',
        'currency' => 'string /^[A-Z]{3}$/i',
        'gateway' => 'string',
        'total_price' => 'float',
        'browser_ip' => 'string /^(:?[0-9a-f]{0,5}[:\.])+[0-9a-f]{0,4}$/i',

        'customer' => 'object \Customer',
        'line_items' => 'array object \LineItem',
        
        'name' => 'string optional',
        'additional_emails' => 'array string /^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,20}$/i optional',
        'note' => 'string optional',
        'number' => 'number optional',
        'order_number' => 'number optional',
        'cancel_reason' => 'string optional',
        'cancelled_at' => 'date optional',
        'closed_at' => 'date optional',
        'cart_token' => 'string optional',
        'checkout_token' => 'string optional',
        'token' => 'string optional',
        'referring_site' => 'string optional',
        'confirmed' => 'boolean optional',
        'buyer_accepts_marketing' => 'boolean optional',
        'financial_status' => 'string optional',
        'fulfillment_status' => 'string optional',
        'landing_site' => 'string optional',
        'landing_site_ref' => 'string optional',
        'location_id' => 'string optional',
        'source' => 'string optional',
        'source_identifier' => 'string optional',
        'source_name' => 'string optional',
        'source_url' => 'string optional',
        'subtotal_price' => 'float optional',
        'taxes_included' => 'boolean optional',
        'total_discounts' => 'float optional',
        'total_line_items_price' => 'float optional',
        'total_price_usd' => 'float optional',
        'total_tax' => 'float optional',
        'total_weight' => 'float optional',
        'user_id' => 'string optional',
        'processing_method' => 'string optional',
        'checkout_id' => 'string optional',
        'tags' => 'string optional',
        'vendor_id' => 'string optional',
        'vendor_name' => 'string optional',

        'shipping_address' => 'object \Address optional',
        'billing_address' => 'object \Address optional',
        'payment_details' => 'object \PaymentDetails optional',
        'client_details' => 'object \ClientDetails optional',
        'discount_codes' => 'array object \DiscountCode optional',
        'shipping_lines' => 'array object \ShippingLine optional',
        'note_attributes' => 'array object \Attribute optional',
        'tax_lines' => 'array object \TaxLine optional',

        'authorization_error' => 'object \AuthorizationError optional',
        'nocharge_amount' => 'object \RefundDetails optional',

        'decision' => 'object \DecisionDetails optional'
    );
}
