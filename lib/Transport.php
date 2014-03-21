<?php namespace riskified\sdk {
abstract class Transport {

	protected $url;
	protected $domain;
	protected $authToken;
		
	abstract protected function sendJsonRequest($order);
	
	public function __construct($url, $domain, $authToken) {
		$this->url = $url;
		$this->domain = $domain;
		$this->authToken = $authToken;
	}
	
	public function sendRequest($order) {
	    $invalids = $order->validate();
	   	if ($invalids) {
    		return $this->error_response('Validation Failed', $invalids);
    	} 
		return $this->sendJsonRequest($order);
	}

	protected function calc_hmac($dataString) {
		return hash_hmac('sha256', $dataString, $this->authToken);
	}
	
	protected function error_response($message, $details) {
		return array( 'error' => array( 'message' => $message, 'details' => $details ) );
	}
}
}?>