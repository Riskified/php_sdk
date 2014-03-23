<?php
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
namespace Riskified\OrderWebhook\Model;

/**
 * Class Address
 * @package Riskified
 */
class Address extends AbstractModel {

    protected $_fields = [
        'first_name' => 'string',
        'last_name' => 'string',
        'name' => 'string optional',
        'company' => 'string',
        'address1' => 'string',
        'address2' => 'string optional',
        'city' => 'string',
        'country_code' => 'string /^[A-Z]{2}$/i',
        'country' => 'string',
        'province_code' => 'string /^[A-Z]{2}$/i optional',
        'province' => 'string optional',
        'zip' => 'string optional',
        'phone' => 'string'
    ];
}
