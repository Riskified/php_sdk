<?php namespace Riskified\DecisionNotification\Model;
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
     * @var string Status of Order
     */
    public $oldStatus;
    /**
     * @var string Description of Decision
     */
    public $description;
    /**
     * @var string Category of Decision
     */
    public $category;
    /**
     * @var string Decision Code of Decision
     */
    public $decisionCode;

    protected $signature;
    protected $headers;
    protected $body;

    /**
     * Inits and validates the request.
     * @param $signature Signature An instance of a Signature class that handles authentication
     * @param $headers array Associative array of HTTP headers
     * @param $body string The raw body of the Request
     * @throws NotificationException on issues with the request
     */
    public function __construct($signature, $headers, $body) {
        $this->signature = $signature;
        $this->headers = $headers;
        $this->body = $body;

        $this->test_authorization();
        $this->parse_body();
    }

    /**
     * assets that the request authentication is valid
     * @throws \Riskified\DecisionNotification\Exception\AuthorizationException on HMAC mismatch
     */
    protected function test_authorization() {
        $signature = $this->signature;
        $remote_hmac = $this->headers[$signature::HMAC_HEADER_NAME];
        $local_hmac = $signature->calc_hmac($this->body);
        if ($remote_hmac != $local_hmac)
            throw new Exception\AuthorizationException($this->headers, $this->body, $local_hmac, $remote_hmac);
    }

    /**
     * iterates over the order object and sets the response fields
     * @param $order object to format
     */
    private function set_responses($order) {
      $response_values = array(
        'id',
        'status',
        'oldStatus',
        'description',
        'category',
        'decisionCode'
      );
      foreach($order as $key => $value) {
        $formatted_key = $this->snake_to_camel($key);
        if (in_array($formatted_key, $response_values)) {
          $this->$formatted_key = $value;
        }
      }
    }

    /**
     * takes a string and changes snakecase to camelcase
     * @param string to format
     * @return string
     */
    private function snake_to_camel($string) {
      return lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $string))));
    }

    /**
     * extracts parameters from HTTP POST body
     * @throws \Riskified\DecisionNotification\Exception\BadPostJsonException on bad or missing parameters
     */
    protected function parse_body() {
        $body = json_decode($this->body);
        if (!array_key_exists('order', $body))
            throw new Exception\BadPostJsonException($this->headers, $this->body);

        $order = $body->{'order'};
        if (!array_key_exists('id', $order) || !array_key_exists('status', $order))
            throw new Exception\BadPostJsonException($this->headers, $this->body);

        $this->set_responses($order);
    }
}
