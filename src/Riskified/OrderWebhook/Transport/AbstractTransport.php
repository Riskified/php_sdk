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

use Riskified\Common\Riskified;

/**
 * Class AbstractTransport
 * Transport to submit orders to Rsikified
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
     */
    abstract protected function send_json_request($order);

    /**
     * set up transport
     * @param $signature object Signature object for authentication handling
     * @param $url string Riskified endpoint (optional). Use staging.riskified.com for development
     */
    public function __construct($signature, $url = 'wh.riskified.com') {
        $this->signature = $signature;
        $this->url = $url;
        $this->user_agent = 'riskified_php_sdk/' . Riskified::VERSION;
    }

    /**
     * Submit an Order to Riskified for review
     * @param $order object Order to submit
     * @return object Response object
     * @throws \Riskified\Common\Exception\BaseException on any issue
     */
    public function submitOrder($order) {
        if ($order->validate())
            return $this->send_json_request($order);
    }

    /**
     * full path to the Riskified endpoint
     * @return string
     */
    protected function full_path() {
        $protocol = ($this->use_https) ? 'https' : 'http';
        return "$protocol://$this->url/webhooks/merchant_order_created";
    }

    /**
     * construct headers for request
     * @param $data_string string body of request
     * @return array headers
     */
    protected function headers($data_string) {
        $signature = $this->signature;
        return array(
            'Content-Type: application/json',
            'Content-Length: '.strlen($data_string),
            $signature::SHOP_DOMAIN_HEADER_NAME.':'.Riskified::$domain,
            $signature::SUBMIT_HEADER_NAME.':true',
            $signature::HMAC_HEADER_NAME.':'.$this->signature->calc_hmac($data_string)
        );
    }
}