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

 /**
 * Class CurlTransport
 * @package Riskified
 */
class CurlTransport extends AbstractTransport {

    /**
     * @var int
     */
    public $timeout = 10;
    public $dns_cache = true;
    public $user_agent = "riskified_sdk_php";

    /**
     */
    public function __construct($url){
        parent::__construct($url);
        // Make sure the user agent is prefixed by the SDK version
        $this->user_agent = 'aws-sdk-php2/' . Riskified::VERSION;
    }
    /**
     * @param $order
     * @return array|mixed|object|\stdClass
     */
    protected function send_json_request($order) {
        $data_string = $order->toJson();

        $ch = curl_init($this->full_path());
        $options = [
            CURLOPT_POSTFIELDS => $data_string,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $this->headers($data_string),
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_USERAGENT => $this->user_agent,
            CURLOPT_TIMEOUT => $this->timeout,
            CURLOPT_DNS_USE_GLOBAL_CACHE => $this->dns_cache
        ];
        curl_setopt_array($ch, $options);

        $body = curl_exec($ch);
        if (curl_errno($ch))
            return $this->error_response('cURL Error '.curl_errno($ch), curl_error($ch));

        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $this->json_response($body, $status);
    }

    /**
     * @param $data_string
     * @return array
     */
    private function headers($data_string) {
        return [
            'Content-Type: application/json',
            'Content-Length: '.strlen($data_string),
            'X_RISKIFIED_SHOP_DOMAIN:'.Riskified::$domain,
            'X_RISKIFIED_SUBMIT_NOW:true',
            'X_RISKIFIED_HMAC_SHA256:'.$this->calc_hmac($data_string)
        ];
    }

    /**
     * @param $body
     * @param $status
     * @return object|\stdClass
     */
    private function json_response($body, $status) {
        $json = new \stdClass();
        $json->http_status = $status;

        if (( $response = json_decode($body) )) {
            $json->response = $response;
        } else {
            $error = $this->error_response('Malformed JSON', $body);
            $json = (object) array_merge((array) $json, (array) $error);
        }

        return $json;
    }
}