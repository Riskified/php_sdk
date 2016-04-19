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
 * Class LineItem
 * data model of line items purchased
 * @package Riskified\OrderWebhook\Model
 */
class LineItem extends AbstractModel {

    protected $_fields = array(
        'price' => 'float',
        'quantity' => 'number',
        'title' => 'string',

        'sku' => 'string optional',
        'product_id' => 'string optional',
        'fulfillment_service' => 'string optional',
        'fulfillment_status' => 'string optional',
        'grams' => 'float optional',
        'id' => 'string optional',
        'variant_id' => 'string optional',
        'variant_title' => 'string optional',
        'variant_inventory_management' => 'string optional',
        'vendor' => 'string optional',
        'name' => 'string optional',
        'requires_shipping' => 'boolean optional',
        'taxable' => 'boolean optional',
        'product_exists' => 'boolean optional',
        'condition' => 'string optional',
        'product_type' => 'string optional',
        'brand' => 'string optional',

        /* add this field when the (digital) goods are to be delivered in future date */
        'delivered_at' => 'date optional',

        /* adding product's category to php sdk */
        'category' => 'string optional',
        'sub_category' => 'string optional',

        /* fields for ticket industry */
        'event_name' => 'string optional',
        'event_section_name' => 'string optional',
        'event_date' => 'date optional',
        'event_country' => 'string optional',
        'event_city' => 'string optional',
        'event_location' => 'string optional',

        /* fields for giftcard industry */
        'photo_url' => 'string optional',
        'photo_uploaded' => 'boolean optional',
        'greeting_photo_url' => 'string optional',
        'display_name' => 'string optional',
        'message' => 'string optional',
        'greeting_message' => 'string optional',
        'card_type' => 'string optional',
        'card_subtype' => 'string optional',
        'sender_name'=> 'string optional',

        /* fields for travel industry */
        'leg_id'=> 'string optional',
        'departure_port_code'=> 'string optional',
        'departure_city'=> 'string optional',
        'departure_country_code'=> 'string optional',
        'arrival_port_code'=> 'string optional',
        'arrival_city'=> 'string optional',
        'arrival_country_code'=> 'string optional',
        'departure_date'=> 'datetime optional',
        'arrival_date'=> 'datetime optional',
        'carrier_name'=> 'string optional',
        'carrier_code'=> 'string optional',
        'route_index'=> 'number optional',
        'leg_index'=> 'number optional',
        'ticket_class'=> 'string optional',
        'transport_method'=> 'string optional',

        /* fields for accommodation industry */
        'room_type'=> 'string optional',
        'city'=> 'string optional',
        'country_code'=> 'string optional',
        'check_in_date'=> 'date optional',
        'check_out_date'=> 'date optional',
        'rating'=> 'float optional',
        'number_of_guests'=> 'number optional',
        'cancellation_policy' => 'string optional',
        'accommodation_type' => 'string optional',


        'properties' =>'array object \Attribute optional',
        'tax_lines' => 'array object \TaxLine optional',
        'seller' => 'object \Seller optional',

        'delivered_to'=> 'string optional',

        'release_date' => 'date optional',
        'size' => 'string optional'
    );
}