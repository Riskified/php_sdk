<?php
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
namespace Riskified\OrderWebhook\Transport;
use Riskified\Common\Riskified;
use Riskified\Common\Signature\HttpDataSignature;
/**
 * Class AbstractTransport
 * @package Riskified
 */
abstract class AbstractTransport {

    /**
     * @var
     */
    public $use_https = true;
    protected $url;
    protected $user_agent = 'riskified php_sdk v1.0';
    protected $signature;
    /**
     * @param $order
     * @return mixed
     */
    abstract protected function send_json_request($order);

    /**
     * @param $domain
     * @param $auth_token
     * @param string $url
     */
    public function __construct($url = 'wh.riskified.com', $signature) {
        $this->url = $url;
        $this->signature = $signature;
    }

    /**
     * @param $order
     * @return array
     */
    public function submitOrder($order) {
        if (( $issues = $order->validate() ))
            return $this->error_response('Validation Failed', $issues);

        return $this->send_json_request($order);
    }

    /**
     * @param $data_string
     * @return array
     */
    protected function headers($data_string) {
        return [
            'Content-Type: application/json',
            'Content-Length: '.strlen($data_string),
            'X_RISKIFIED_SHOP_DOMAIN:'.Riskified::$domain,
            'X_RISKIFIED_SUBMIT_NOW:true',
            'X_RISKIFIED_HMAC_SHA256:'.$this->signature->calc_hmac($data_string)
        ];
    }

    /**
     * @return string
     */
    protected function full_path() {
        $protocol = ($this->use_https) ? 'https' : 'http';
        return "$protocol://$this->url/webhooks/merchant_order_created";
    }

    /**
     * @param $message
     * @param $details
     * @return array
     */
    protected function error_response($message, $details) {
        return ['error' => ['message' => $message, 'details' => $details] ];
    }
}