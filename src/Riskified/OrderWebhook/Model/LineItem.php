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
        'properties' =>'objects \Attribute optional',
        'tax_lines' => 'objects \TaxLine optional'
    );
}