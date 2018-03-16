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
 * Class DisputeDetails
 * data model of an existing order's chargeback dispute details
 * @package Riskified\OrderWebhook\Model
 */
class DisputeDetails extends AbstractModel {

    protected $_fields = array(
        'case_id' => 'string',
        'status' => 'string',
        'disputed_at' => 'datetime',
        'expected_resolution_date' => 'datetime',
        'dispute_type' => 'string',
        'issuer_poc_phone_number' => 'string'
    );
}
