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
 * Class ClientDetails
 * data model of client details of customer placing order
 * @package Riskified\OrderWebhook\Model
 */
class SessionDetails extends AbstractModel {

    protected $_fields = array(
        'created_at' => 'date',
        'cart_token' => 'string',
        'browser_ip' => 'string /^(:?[0-9a-f]{0,5}[:\.])+[0-9a-f]{0,4}$/i',
        'source' => 'string /^(:?desktop_web|mobile_app|mobile_web|other)$/',
        'referring_site' => 'string optional'
    );
}
