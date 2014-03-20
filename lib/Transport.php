<?php
abstract class Transport {

	abstract public function sendRequest($dataString, $url, $domain, $authToken);
	
	public function getHashHmac($dataString, $authToken) {
		return hash_hmac('sha256', $dataString, $authToken);
	}
}
?>