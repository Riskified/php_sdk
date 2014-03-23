<?php namespace Riskified\SDK {
    abstract class AbstractTransport {

        protected $domain;
        protected $auth_token;
        protected $url;
        protected $use_https = true;

        abstract protected function send_json_request($order);

        public function __construct($domain, $auth_token, $url = 'wh.riskified.com') {
            $this->url = $url;
            $this->domain = $domain;
            $this->auth_token = $auth_token;
        }

        public function submitOrder($order) {
            if (( $invalids = $order->validate() ))
                return $this->error_response('Validation Failed', $invalids);

            return $this->send_json_request($order);
        }

        protected function full_path() {
            $protocol = ($this->use_https) ? 'https' : 'http';
            return "$protocol://$this->url/webhooks/merchant_order_created";
        }

        protected function calc_hmac($data_string) {
            return hash_hmac('sha256', $data_string, $this->auth_token);
        }

        protected function error_response($message, $details) {
            return ['error' => ['message' => $message, 'details' => $details] ];
        }
    }
}