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
 * Class Fulfillment
 * data model of fulfillment
 * @package Riskified\OrderWebhook\Model
 */
class Fulfillment extends AbstractModel {

    protected $_fields = array(
        'created_at' => 'date',
        'updated_at' => 'date',
        'id' => 'string',
        'order_id' => 'string',
        'service' => 'string',
        'status' => 'string',

        'tracking_company' => 'string optional',
        'tracking_number' => 'string optional',
        'tracking_url' => 'string optional',
        'line_items' => 'objects \LineItem optional'
    );
}

// "receipt":{"gift_cards":[{"id":732301,"line_item_id":372051657,"masked_code":"\u00b7\u00b7\u00b7\u00b7 \u00b7\u00b7\u00b7\u00b7 \u00b7\u00b7\u00b7\u00b7 gcae"}]}