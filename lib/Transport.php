<?php namespace riskified\sdk {
abstract class Transport {

	protected $domain;
	protected $auth_token;
	protected $url;
		
	abstract protected function send_json_request($order);
	
	public function __construct($domain, $auth_token, $url = 'wh.riskified.com') {
		$this->url = $url;
		$this->domain = $domain;
		$this->auth_token = $auth_token;
	}
	
	public function send_request($order) {
	    $invalids = $order->validate();
	   	if ($invalids) {
    		return $this->error_response('Validation Failed', $invalids);
    	} 
		return $this->send_json_request($order);
	}

	protected function calc_hmac($data_string) {
		return hash_hmac('sha256', $data_string, $this->auth_token);
	}
	
	protected function error_response($message, $details) {
		return array('error' => array('message' => $message, 'details' => $details ));
	}
}
}?>