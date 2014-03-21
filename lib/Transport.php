<?php namespace riskified\sdk {
abstract class Transport {

	protected $url;
	protected $domain;
	protected $authToken;
	
	abstract public function sendRequest($order);
	
	public function __construct($url, $domain, $authToken) {
		$this->url = $url;
		$this->domain = $domain;
		$this->authToken = $authToken;
	}

	protected function calc_hmac($dataString) {
		return hash_hmac('sha256', $dataString, $this->authToken);
	}
	
	protected function error_response($code, $message) {
		return array( 'error' => array( 'code' => $code, 'message' => $message ) );
	}
}
}?>