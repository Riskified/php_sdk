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
 * Class Passenger
 * data model of passenger in order
 * @package Riskified\OrderWebhook\Model
 */
class Passenger extends AbstractModel {

    protected $_fields = array(
        'first_name'               => 'string',
        'last_name'                => 'string',
        'date_of_birth'            => 'date',
        'nationality_code'         => 'string',
        'document_number'          => 'string',
        'document_type'            => 'string',
        'insurance_type'           => 'string optional',
        'insurance_price'          => 'float optional',
        'document_issue_date'      => 'date optional',
        'document_expiration_date' => 'string optional',
        'passenger_type'           => 'string optional'
    );
}