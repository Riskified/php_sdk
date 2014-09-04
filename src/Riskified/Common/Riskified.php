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
    const VERSION = '1.0.3';
    const API_VERSION = '2';

    /**
     * @var string Riskified Shop Domain
     */
    public static $domain;
    /**
     * @var string Riskified Auth_Token
     */
    public static $auth_token;
    /**
     * @var string the Riskified environment to which calls are suppose to be sent
     */
    public static $env;
    /**
     * @var boolean indicates validation should ignore missing key errors
     */
    public static $ignore_missing_keys;


    /**
     * Sets up Riskified credentials. Must be called before any other method can be used.
     * @param $domain string Riskified Shop Domain
     * @param $auth_token string Riskified Auth_Token
     * @param $env string Riskified environment
     * @param $ignore_missing_keys boolean ignores missing keys when true
     */
    public static function init($domain, $auth_token, $env = Env::SANDBOX, $ignore_missing_keys = false) {
        self::$domain = $domain;
        self::$auth_token = $auth_token;
        self::$env = $env;
        self::$ignore_missing_keys = $ignore_missing_keys;
    }

    public static function getHostByEnv(){
        $env = (self::$env == null) ? Env::SANDBOX : self::$env;

        switch ($env){
            case Env::SANDBOX:
                return 'sandbox.riskified.com';
            case Env::STAGING:
                return 's.riskified.com';
            case Env::PROD:
                return 'wh.riskified.com';
            case Env::DEV:
                return 'localhost:3000';
            default:
                return 'localhost:3000';
        }
    }
}