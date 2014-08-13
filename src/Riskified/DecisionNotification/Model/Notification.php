<?php namespace Riskified\DecisionNotification\Model;
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

use Riskified\DecisionNotification\Exception;

/**
 * Class Notification
 * Parses and validates Decision Notification callbacks from Riskified
 * @package Riskified\DecisionNotification\Model
 */
class Notification {

    /**
     * @var string Order ID
     */
    public $id;
    /**
     * @var string Status of Order
     */
    public $status;
    /**
     * @var string Description of Decision
     */
    public $description;

    protected $signature;
    protected $headers;
    protected $headers_map;
    protected $body;

    /**
     * Inits and validates the request.
     * @param $signature Signature An instance of a Signature class that handles authentication
     * @param $headers array A list of HTTP Headers as strings
     * @param $body string The raw body of the Request
     * @throws NotificationException on issues with the request
     */
    public function __construct($signature, $headers, $body) {
        $this->signature = $signature;
        $this->headers = $headers;
        $this->body = $body;

        $this->parse_headers();
        $this->parse_body();
        $this->test_authorization();
    }

    /**
     * Converts array of headers into a key->value map
     * @throws \Riskified\DecisionNotification\Exception\BadHeaderException on malformed headers
     */
    protected function parse_headers() {
        $this->headers_map = array();
        foreach($this->headers as $i => $header) {
            list ($key, $value) = explode(':', $header);
            if (!$key || !$value)
                throw new Exception\BadHeaderException($this->headers, $this->body, $header);
            $header = str_replace('-', '_', strtoupper(trim($key)));
            $this->headers_map[$header] = trim($value);
        }
    }

    /**
     * assets that the request authentication is valid
     * @throws \Riskified\DecisionNotification\Exception\AuthorizationException on HMAC mismatch
     */
    protected function test_authorization() {
        $signature = $this->signature;
        $remote_hmac = $this->headers_map[$signature::HMAC_HEADER_NAME];
        $local_hmac = $signature->calc_hmac($this->body);
        if ($remote_hmac != $local_hmac)
            throw new Exception\AuthorizationException($this->headers, $this->body, $local_hmac, $remote_hmac);
    }

    /**
     * extracts parameters from HTTP POST body
     * @throws \Riskified\DecisionNotification\Exception\BadPostParametersException on bad or missing parameters
     */
    protected function parse_body() {
        $json = json_decode($this->body);
        if (isset($json->{'order'}))
            $order = $json->{'order'};
        if (!$order->{'id'} || !$order->{'status'})
            throw new Exception\BadPostParametersException($this->headers, $this->body);

        foreach($order as $key => $value)
            $this->$key = $value;
    }
}
