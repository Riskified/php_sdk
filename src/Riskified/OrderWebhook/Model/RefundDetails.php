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
 * Class Refund
 * data model of an exisiting order's refund details
 * @package Riskified\OrderWebhook\Model
 */
class RefundDetails extends AbstractModel {

    protected $_fields = array(
        'refund_id' => 'string',
        'amount' => 'double',
        'currency' => 'string /^[A-Z]{3}$/i',

        'refunded_at' => 'date optional',
        'reason' => 'string optional'
    );
}