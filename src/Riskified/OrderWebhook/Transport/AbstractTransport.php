<?php namespace Riskified\OrderWebhook\Transport;
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

use Riskified\Common\Env;
use Riskified\Common\Riskified;

/**
 * Class AbstractTransport
 * A base class for Transports for sending order data to Riskified
 * Orders will be created if the id was never used before, and updated if already created
 * Submission of orders is done by similarly to creation with an addition of a header
 * @package Riskified
 */
abstract class AbstractTransport {

    /**
     * @var boolean set false to use HTTP instead
     */
    public $use_https = true;
    protected $url;
    protected $signature;
    protected $user_agent;

    /**
     * submit an order as json
     * @param $order object Order to send
     * @param $options array optional list of options
     */
    abstract protected function send_json_request($order, $options);

    /**
     * set up transport
     * @param $signature object Signature object for authentication handling
     * @param $url string Riskified endpoint (optional)
     */
    public function __construct($signature, $url = null) {
        $this->signature = $signature;
        $this->url = ($url == null) ? Riskified::getHostByEnv() : $url;
        $this->user_agent = 'riskified_php_sdk/' . Riskified::VERSION;
        $this->use_https = Riskified::$env != Env::DEV;
    }

    /**
     * Submit an Order to Riskified for review
     * @param $order object Order to submit
     * @param $options array optional options array for custom headers
     * @return object Response object
     * @throws \Riskified\Common\Exception\BaseException on any issue
     */
    public function submitOrder($order, $options = array()) {
        if ($order->validate())
            return $this->send_json_request($order, array_merge($options, array('SUBMIT' => true)));
        return null;
    }

    /**
     * Submit an Order to Riskified for review
     * @param $order object Order to submit
     * @param $options array optional options array for custom headers
     * @return object Response object
     * @throws \Riskified\Common\Exception\BaseException on any issue
     */
    public function createOrUpdateOrder($order, $options = array()) {
        if ($order->validate())
            return $this->send_json_request($order, $options);
        return null;
    }

    /**
     * full path to the Riskified endpoint
     * @return string
     */
    public function full_path() {
        $protocol = ($this->use_https) ? 'https' : 'http';
        return "$protocol://$this->url/webhooks/merchant_order_created";
    }

    /**
     * construct headers for request
     * @param $data_string string body of request
     * @param $options array optional config (submit now, list of custom headers)
     * @return array headers
     */
    protected function headers($data_string, $options=array()) {
        $signature = $this->signature;
        $headers = array(
            'Content-Type: application/json',
            'Content-Length: '.strlen($data_string),
            $signature::SHOP_DOMAIN_HEADER_NAME.':'.Riskified::$domain,
            $signature::HMAC_HEADER_NAME.':'.$this->signature->calc_hmac($data_string)
        );
        if (isset($options['SUBMIT']))
            $headers[] = $signature::SUBMIT_HEADER_NAME.':true';

        if (isset($options['headers']))
            $headers = array_merge($headers, $options['headers']);

        return $headers;
    }
}
