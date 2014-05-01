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
 * Class Address
 * data model of shipping/billing address
 * @package Riskified
 */
class Address extends AbstractModel {

    protected $_fields = array(
        'first_name' => 'string',
        'last_name' => 'string',
        'city' => 'string',
        'phone' => 'string',
        'country' => 'string',
        'country_code' => 'string /^[A-Z]{2}$/i',

        'name' => 'string optional',
        'company' => 'string optional',
        'address1' => 'string optional',
        'address2' => 'string optional',
        'province' => 'string optional',
        'province_code' => 'string /^[A-Z]{2}$/i optional',
        'zip' => 'string optional',
        'latitude' => 'float optional',
        'longitude' => 'float optional',
    );
}