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

    const SHOP_DOMAIN_HEADER_NAME = 'X-RISKIFIED-SHOP-DOMAIN';
    const SUBMIT_HEADER_NAME = 'X-RISKIFIED-SUBMIT-NOW';
    const HMAC_HEADER_NAME = 'X-RISKIFIED-HMAC-SHA256';

    /**
     * Calculates the HMAC SHA256
     * @param $body string Body of POST request to hash
     * @return string Value for HMAC_HEADER_NAME
     */
    public function calc_hmac($body) {
        return hash_hmac('sha256', $body, Riskified::$auth_token);
    }

} 