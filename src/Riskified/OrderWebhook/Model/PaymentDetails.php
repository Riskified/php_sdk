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
 * Class PaymentDetails
 * data model of payment (credit card) details
 * @package Riskified\OrderWebhook\Model
 */
class PaymentDetails extends AbstractModel {

    protected $_fields = array(
        'credit_card_bin' => 'string',
        'avs_result_code' => 'string /^.+$/i',
        'cvv_result_code' => 'string /^.+$/i',
        'credit_card_number' => 'string',
        'credit_card_company' => 'string',
        'credit_card_token' => 'string',

        'payer_email' => 'string optional',
        'payer_status' => 'string optional',
        'payer_address_status' => 'string optional',
        'protection_eligibility' => 'string optional',
        'payment_status' => 'string optional',
        'pending_reason' => 'string optional',
        'authorization_id' => 'string optional'
    );
}