<?php namespace Riskified\Common;
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

/**
 * Class Riskified
 * @package Riskified\Common
 */
class Riskified {

    const VERSION = '1.0.0';

    /**
     * @var string Riskified Shop Domain
     */
    public static $domain;
    /**
     * @var string Riskified Auth_Token
     */
    public static $auth_token;

    /**
     * Sets up Riskified credentials. Must be called before any other method can be used.
     * @param $domain string Riskified Shop Domain
     * @param $auth_token Riskified Auth_Token
     */
    public static function init($domain, $auth_token) {
        self::$domain = $domain;
        self::$auth_token = $auth_token;
    }
}