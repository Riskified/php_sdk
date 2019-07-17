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
 * Class AuthenticationResult
 * data model of Result of Authentication via 3DS
 * @package Riskified\OrderWebhook\Model
 */
class AuthenticationResult extends AbstractModel
{

    protected $_fields = array(
        'created_at' => 'date optional',
        'eci' => 'string /^(:?05|06|07)$/',
        'cavv' => 'string optional',
        'trans_status' => 'string /^(:?Y|N|U|A|C|D|R|I)$/ optional',
        'trans_status_reason' => 'string /^(:?01|02|03|04|05|06|07|08|09|10|11|12|13|14|15|16|17|18|19|20|21|22|23|24|25|26|27|80|99)$/ optional'
    );

}