<?php namespace Riskified\Common\Signature;
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

use Riskified\Common\Riskified;

/**
 * Class HttpDataSignature
 * Encapsulate the signature logic for HTTP incoming and outgoing messages using the HMAC SHA256 with the auth_token as
 * the key
 * @package Riskified\Common\Signature
 */
class HttpDataSignature {
    /**
     * @param $data_string
     * @return string
     */
    public function calc_hmac($data_string) {
        return hash_hmac('sha256', $data_string, Riskified::$auth_token);
    }

} 