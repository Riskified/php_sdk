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
use Riskified\Common\Validations;

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
     * @param $json object Order to send
     * @param $endpoint String API endpoint to send request
     */
    abstract protected function send_json_request($json, $endpoint);

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
     * @return object Response object
     * @throws \Riskified\Common\Exception\BaseException on any issue
     */
    public function submitOrder($order) {
        return $this->send_order($order, 'submit', true);
    }

    /**
     * Send an Order to Riskified, will be reviewed based on current plan
     * @param $order object Order to send
     * @return object Response object
     * @throws \Riskified\Common\Exception\BaseException on any issue
     */
    public function createOrder($order) {
        return $this->send_order($order, 'create', true);
    }

    /**
     * Update an existing order
     * @param $order object Order with updated fields
     * @return object Response object
     * @throws \Riskified\Common\Exception\BaseException on any issue
     */
    public function updateOrder($order) {
        return $this->send_order($order, 'update', false);
    }

    /**
     * Cancels an existing order
     * @param $order object Order with id, cancelled_at, cancel_reason fields
     * @return object Response object
     * @throws \Riskified\Common\Exception\BaseException on any issue
     */
    public function cancelOrder($order) {
        return $this->send_order($order, 'cancel', false);
    }

    /**
     * Partially refunds an existing order
     * @param $order object Order with id and refunds object
     * @return object Response object
     * @throws \Riskified\Common\Exception\BaseException on any issue
     */
    public function refundOrder($order) {
        return $this->send_order($order, 'refund', false);
    }

    public function sendHistoricalOrders($orders) {
        $joined = join(',',array_map(function($order) { return $order->toJson(); }, $orders));
        $json = '{"orders":['.$joined.']}';
        return $this->send_json_request($json, 'historical');
    }

    protected function send_order($order, $endpoint, $enforce_required_keys) {
        if ($this->validate($order, $enforce_required_keys)) {
            $json = '{"order":' . $order->toJson() . '}';
            return $this->send_json_request($json, $endpoint);
        }
        return null;
    }

    protected function validate($order, $enforce_required_keys=true) {
        if (Riskified::$validations == Validations::SKIP)
            return true;
        return $order->validate($enforce_required_keys && Riskified::$validations == Validations::ALL);
    }

    /**
     * path prefix to the Riskified endpoint
     * @return string
     */
    protected function endpoint_prefix() {
        $protocol = ($this->use_https) ? 'https' : 'http';
        return "$protocol://$this->url/api/";
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
            $signature::HMAC_HEADER_NAME.':'.$this->signature->calc_hmac($data_string),
            'Accept: application/vnd.riskified.com; version='.Riskified::API_VERSION
        );
    }
}
