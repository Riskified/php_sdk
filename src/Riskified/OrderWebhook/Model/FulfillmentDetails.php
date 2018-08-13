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

namespace Riskified\OrderWebhook\Model;

/**
 * Class FulfillmentDetails
 * data model of fulfillment
 * @package Riskified\OrderWebhook\Model
 */
class FulfillmentDetails extends AbstractModel {

    protected $_fields = array(
        'created_at' => 'date',
        'fulfillment_id' => 'string',
        'status' => 'string',

        'tracking_company' => 'string optional',
        'tracking_numbers' => 'string optional',
        'tracking_urls' => 'string optional',
        'message' => 'string optional',
        'receipt' => 'string',

        'line_items' => 'array object \LineItem optional'
    );
}