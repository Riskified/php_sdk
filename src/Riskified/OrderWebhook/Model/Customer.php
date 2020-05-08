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
 * Class Customer
 * data model of customer in order
 * @package Riskified\OrderWebhook\Model
 */
class Customer extends AbstractModel {

    protected $_fields = array(
        'email' => "string /^[a-z0-9,!#\$%&'\*\+\/=\?\^_`\{\|}~-]+(?:\.[a-z0-9,!#\$%&'\*\+\/=\?\^_`\{\|}~-]+)*@[a-z0-9-]+(?:\.[a-z0-9-]+)*\.(?:[a-z]{2,})$/i",
        'first_name' => 'string',
        'last_name' => 'string',

        'created_at' => 'date optional',
        'updated_at' => 'date optional',
        'id' => 'string optional',
        'group_id' => 'string optional',
        'group_name' => 'string optional',
        'note' => 'string optional',
        'orders_count' => 'number optional',
        'verified_email' => 'boolean optional',
        'verified_email_at' => 'date optional',
        'phone' => 'string optional',
        'verified_phone' => 'boolean optional',
        'verified_phone_at' => 'date optional',
        'date_of_birth' => 'date optional',
        'gender' => 'string optional',
        'user_name' => 'string optional',
        'accepts_marketing' => 'boolean optional',
        'last_order_id' => 'string optional',
        'last_order_name' => 'string optional',
        'state' => 'string optional',
        'total_spent' => 'float optional',
        'tags' => 'string optional',
        'account_type' => 'string optional',
        'first_purchase_at' => 'date optional',
        'linked_accounts' => 'number optional',

        'default_address' => 'object \Address optional',
        'social' => 'array object \SocialDetails optional',

        'buy_attempts' => 'number optional',
        'sell_attempts' => 'number optional',

        'document_id' => 'string optional',
        'document_type' => 'string optional',

        'address' => 'object \Address optional'
    );
}
