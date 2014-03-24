<?php namespace Riskified\OrderWebhook\Transport;

use Riskified\Common\Riskified;

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
    protected $signature;
    protected $user_agent;

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
    public function __construct($signature, $url = 'wh.riskified.com') {
        $this->signature = $signature;
        $this->url = $url;
        $this->user_agent = 'riskified_php_sdk/' . Riskified::VERSION;
    }

    /**
     * @param $order
     * @return array
     */
    public function submitOrder($order) {
        if ($order->validate())
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
     * @return array
     */
    protected  function headers($data_string) {
        return [
            'Content-Type: application/json',
            'Content-Length: '.strlen($data_string),
            'X_RISKIFIED_SHOP_DOMAIN:'.Riskified::$domain,
            'X_RISKIFIED_SUBMIT_NOW:true',
            'X_RISKIFIED_HMAC_SHA256:'.$this->signature->calc_hmac($data_string)
        ];
    }

//    /**
//     * @param $data_string
//     * @return string
//     */
//    protected function calc_hmac($data_string) {
//        return hash_hmac('sha256', $data_string, $this->auth_token);
//    }
//
//    /**
//     * @param $message
//     * @param $details
//     * @return array
//     */
//    protected function error_response($message, $details) {
//        return ['error' => ['message' => $message, 'details' => $details] ];
//    }
}