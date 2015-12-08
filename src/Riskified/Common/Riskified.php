<?php namespace Riskified\Common;

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
 * Class Riskified
 * @package Riskified\Common
 */
class Riskified {
    const VERSION = '1.6.1';
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
     * @var string validation mode [SKIP, IGNORE_MISSING, ALL]
     */
    public static $validations;


    /**
     * Sets up Riskified credentials. Must be called before any other method can be used.
     * @param $domain string Riskified Shop Domain
     * @param $auth_token string Riskified Auth_Token
     * @param $env string Riskified environment
     * @param $validations string SDK validation mode
     */
    public static function init($domain, $auth_token, $env = Env::SANDBOX, $validations = Validations::IGNORE_MISSING) {
        self::$domain = $domain;
        self::$auth_token = $auth_token;
        self::$env = $env;

        // for backward compatibility (versions 1.0.*)
        if (is_bool($validations))
            $validations = ($validations) ? Validations::SKIP : Validations::ALL;

        self::$validations = $validations;

        // suppress timezone warnings:
        date_default_timezone_set(@date_default_timezone_get());
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