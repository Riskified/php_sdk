<?php namespace Riskified\OrderWebhook\Transport;
/**
 * Class AbstractTransport
 * @package Riskified
 */
abstract class AbstractTransport {

    /**
     * @var
     */
    public $use_https = true;
    protected $domain;
    protected $auth_token;
    protected $url;
    protected $user_agent = 'riskified php_sdk v1.0';

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
    public function __construct($domain, $auth_token, $url = 'wh.riskified.com') {
        $this->url = $url;
        $this->domain = $domain;
        $this->auth_token = $auth_token;
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
     * @return string
     */
    protected function full_path() {
        $protocol = ($this->use_https) ? 'https' : 'http';
        return "$protocol://$this->url/webhooks/merchant_order_created";
    }

    /**
     * @param $data_string
     * @return string
     */
    protected function calc_hmac($data_string) {
        return hash_hmac('sha256', $data_string, $this->auth_token);
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