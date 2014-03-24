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
 * Class Customer
 * @package Riskified
 * @property string $created_at
 */
class Customer extends AbstractModel {

    protected $_fields = [
        'created_at' => 'date optional',
        'email' => 'string /^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i',
        'first_name' => 'string',
        'last_name' => 'string',
        'id' => 'string',
        'note' => 'string optional',
        'orders_count' => 'number optional',
        'verified_email' => 'boolean optional',
    ];
}