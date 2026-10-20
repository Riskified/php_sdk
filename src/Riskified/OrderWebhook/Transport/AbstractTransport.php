<?php

/**
 * Copyright 2013-2026 Riskified.com, Inc. or its affiliates. All Rights Reserved.
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

use Riskified\Common\Env;
use Riskified\Common\Riskified;
use Riskified\Common\Validations;
use Riskified\OrderWebhook\Model\Checkout;
use Riskified\OrderWebhook\Model\Order;

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
    /**
     * @var string host used by every request that does not name a host family.
     *      Set once, at construction. Never reassigned - see $flow_strategy.
     */
    protected $url;
    /**
     * @var string|null host family of the request currently being dispatched, or null
     *      when the request uses $url. Scoped to a single call by on_host().
     */
    private $flow_strategy = null;
    protected $signature;
    protected $user_agent;

    /**
     * submit an order as json
     * @param $json object Order to send
     * @param $endpoint String API endpoint to send request
     */
    abstract protected function send_json_request($json, $endpoint);

    /**
     * submit an account action as json
     * @param $json object account action to send
     * @param $endpoint String API endpoint to send request
     */
    abstract protected function send_account_json_request($json, $endpoint);

    /**
     * set up transport
     * @param $signature object Signature object for authentication handling
     * @param $url string Riskified endpoint (optional)
     */
    public function __construct($signature, $url = null) {
        $this->signature = $signature;
        $this->url = ($url == null) ? Riskified::getHost() : $url;
        $this->user_agent = 'riskified_php_sdk/' . Riskified::VERSION;
        $this->use_https = Riskified::$env != Env::DEV;
    }

    /**
     * Update a merchant's settings
     * @param $settings object named 'settings' with a key-value structure
     * @return object Response object
     * @throws \Riskified\Common\Exception\BaseException on any issue
     */
    public function updateMerchantSettings($settings) {
        return $this->send_settings($settings);
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
     * Send an Order to Riskified, will be synchronously reviewed based on current plan
     * @param $order object Order to send
     * @return object Response object
     * @throws \Riskified\Common\Exception\BaseException on any issue
    */
    public function decideOrder($order) {
        return $this->on_host('sync', function () use ($order) {
            return $this->send_order($order, 'decide', true);
        });
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

    /**
     * Send order fulfillment status
     * @param $fulfillment object Fulfillment with order id and fulfillment details
     * @return object Response object
     * @throws \Riskified\Common\Exception\BaseException on any issue
     */
    public function fulfillOrder($fulfillment) {
        return $this->send_order($fulfillment, 'fulfill', true);
    }

    /**
     * Send order decision status
     * @param $decision object Decision on the order. reports riskified about what was your decision on the order.
     * @return object Response object
     * @throws \Riskified\Common\Exception\BaseException on any issue
     */
    public function decisionOrder($decision) {
        return $this->send_order($decision, 'decision', true);
    }

    /**
     * Cancels an existing order
     * @param $chargeback object ChargebackOrder with id and chargeback details
     * @return object Response object
     * @throws \Riskified\Common\Exception\BaseException on any issue
     */
    public function chargebackOrder($chargeback) {
        return $this->send_order($chargeback, 'chargeback', false);
    }

    public function advise(Checkout $checkout) {
        return $this->send_checkout($checkout, 'advise');
    }

    /**
     * Send a checkout decision request to Riskified.
     *
     * Posts to /api/decide, which lives on the sync host family - the same as
     * decideOrder(). The C# reference has exactly one /api/decide call site and it is
     * unconditionally FlowStrategy.Sync (sdk_net @ 9165cf5,
     * Orders/OrdersGateway.cs:142); Sandbox has no sync host and falls back to the
     * default one, as documented in docs/flows/00-shared-contract.md section 3.
     * @param $order object Order to send
     * @return object Response object
     * @throws \Riskified\Common\Exception\BaseException on any issue
     */
    public function checkout_decide(Order $order) {
        return $this->on_host('sync', function () use ($order) {
            return $this->send_order($order, 'decide', false);
        });
    }

    /**
     * Send a Checkout to Riskified
     * @param $checkout object Checkout to send
     * @return object Response object
     * @throws \Riskified\Common\Exception\BaseException on any issue
     */
    public function createCheckout($checkout) {
        return $this->send_checkout($checkout, 'checkout_create');
    }

    /**
     * Notify that a Checkout failed
     * @param $checkout object Checkout to send (with PaymentDetails that include AuthotizationError field)
     * @return object Response object
     * @throws \Riskified\Common\Exception\BaseException on any issue
     */
    public function deniedCheckout($checkout) {
        return $this->send_checkout($checkout, 'checkout_denied');
    }

    /**
     * Check eligibility for Deco payment
     * @param $order object Order to send (only order id required)
     * @return object Response object
     * @throws \Riskified\Common\Exception\BaseException on any issue
     */
    public function eligible($order) {
        return $this->on_host('deco', function () use ($order) {
            return $this->send_order($order, 'eligible', false);
        });
    }

    /**
     * Opt-in to Deco payment
     * @param $order object Order to send (only order id required)
     * @return object Response object
     * @throws \Riskified\Common\Exception\BaseException on any issue
     */
    public function opt_in($order) {
        return $this->on_host('deco', function () use ($order) {
            return $this->send_order($order, 'opt_in', false);
        });
    }

    public function login($login) {
        return $this->on_host('account', function () use ($login) {
            return $this->send_account_event($login, 'login');
        });
    }

    public function customerCreate($customer_create) {
        return $this->on_host('account', function () use ($customer_create) {
            return $this->send_account_event($customer_create, 'customer_create');
        });
    }

    public function verification($event) {
        return $this->on_host('account', function () use ($event) {
            return $this->send_account_event($event, 'verification');
        });
    }

    public function customerUpdate($customer_update) {
        return $this->on_host('account', function () use ($customer_update) {
            return $this->send_account_event($customer_update, 'customer_update');
        });
    }

    public function logout($logout) {
        return $this->on_host('account', function () use ($logout) {
            return $this->send_account_event($logout, 'logout');
        });
    }

    public function resetPasswordRequest($reset_password_request) {
        return $this->on_host('account', function () use ($reset_password_request) {
            return $this->send_account_event($reset_password_request, 'reset_password');
        });
    }

    public function wishlistChanges($wishlist_changes) {
        return $this->on_host('account', function () use ($wishlist_changes) {
            return $this->send_account_event($wishlist_changes, 'wishlist');
        });
    }

    public function redeem($redeem) {
        return $this->on_host('account', function () use ($redeem) {
            return $this->send_account_event($redeem, 'redeem');
        });
    }

    public function sendHistoricalOrders($orders) {
        $joined = join(',', array_map(function ($order) {
            return $order->toJson();
        }, $orders));
        $json = '{"orders":[' . $joined . ']}';
        return $this->send_json_request($json, 'historical');
    }

    protected function send_order($order, $endpoint, $enforce_required_keys) {
        if ($this->validate($order, $enforce_required_keys)) {
            $json = '{"order":' . $order->toJson() . '}';
            return $this->send_json_request($json, $endpoint);
        }
        return null;
    }

    protected function send_account_event($accountEvent, $endpoint, $enforce_required_keys = true) {
        if ($this->validate($accountEvent, $enforce_required_keys)) {
            $json = $accountEvent->toJson();
            return $this->send_account_json_request($json, $endpoint);
        }
        return null;
    }

    protected function send_settings($settings) {
        $json = $settings->toJson();
        return $this->send_json_request($json, 'settings');
    }

    protected function send_checkout($checkout, $endpoint) {
        if ($this->validate($checkout, false)) {
            $json = '{"checkout":' . $checkout->toJson() . '}';
            return $this->send_json_request($json, $endpoint);
        }
        return null;
    }

    protected function validate($order, $enforce_required_keys = true) {
        if (Riskified::$validations == Validations::SKIP) {
            return true;
        }
        return $order->validate($enforce_required_keys && Riskified::$validations == Validations::ALL);
    }

    /**
     * Dispatch one request against a named host family without mutating the transport.
     *
     * The strategy lives for the duration of $send and no longer: a transport instance
     * that has just sent an Account Secure request must still send the next order to
     * the orders host. Mutating $this->url here - as this class used to - made host
     * selection sticky, so any later call on the same instance silently cross-posted to
     * whichever host was chosen last.
     *
     * @param string $flow_strategy host family, e.g. 'sync', 'deco', 'account'
     * @param callable $send performs the request
     * @return mixed whatever $send returns
     */
    private function on_host($flow_strategy, $send) {
        $previous_flow_strategy = $this->flow_strategy;
        $this->flow_strategy = $flow_strategy;
        try {
            return call_user_func($send);
        } finally {
            $this->flow_strategy = $previous_flow_strategy;
        }
    }

    /**
     * Host for the request currently being dispatched, resolved per call.
     * @return string
     */
    protected function host() {
        if ($this->flow_strategy === null) {
            return $this->url;
        }
        return Riskified::getHost($this->flow_strategy);
    }

    /**
     * path prefix to the Riskified endpoint
     * @param $routing
     * @return string
     */
    protected function endpoint_prefix($routing = 'api') {
        $protocol = ($this->use_https) ? 'https' : 'http';
        $host = $this->host();
        return "$protocol://$host/$routing/";
    }

    /**
     * construct headers for request
     * @param $data_string string body of request
     * @return array headers
     */
    protected function headers($data_string) {
        $signature = $this->signature;

        return array(
            'api-version: 2',
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data_string),
            $signature::SHOP_DOMAIN_HEADER_NAME . ':' . Riskified::$domain,
            $signature::HMAC_HEADER_NAME . ':' . $this->signature->calc_hmac($data_string),
            'Accept: application/vnd.riskified.com; version=' . Riskified::API_VERSION
        );
    }
}
