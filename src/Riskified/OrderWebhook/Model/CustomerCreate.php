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
 * Class CustomerCreate
 * data model for CustomerCreate action, includes nested models
 * @package Riskified\OrderWebhook\Model
 */
class CustomerCreate extends AbstractModel {

    protected $_fields = array(
        'customer_id' => 'string',
        'phone_mandatory' => 'boolean optional',
        'referrer_customer_id' => 'string optional',
        'social_signup_type' => 'string /^(:?facebook|google|linkedin|twitter|yahoo|other)$/ optional',
        'client_details' => 'object \ClientDetails',
        'session_details' => 'object \SessionDetails',
        'customer' => 'object \Customer',
        'payment_details' => 'array object \PaymentDetails optional',
        'billing_address' => 'array object \Address optional',
        'shipping_address' => 'array object \Address optional',
        'vendor_name' => 'string optional'
    );
}
