<?php namespace riskified\sdk {
class CurlTransport extends Transport {

	public $timeout = 10;
	public $dns_cache = true;
        
    protected function send_json_request($order) {   
		$data_string = $order->to_json();
		$hmac = $this->calc_hmac($data_string);   
		$headers = array(
			'Content-Type: application/json',
			'Content-Length: ' . strlen($data_string),
			'X_RISKIFIED_SHOP_DOMAIN:'.$this->domain,
			'X_RISKIFIED_SUBMIT_NOW:true',
			'X_RISKIFIED_HMAC_SHA256:'.$hmac
		);
		
        $ch = curl_init("http://$this->url/webhooks/merchant_order_created");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);    
		curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);    
		curl_setopt($ch, CURLOPT_DNS_USE_GLOBAL_CACHE, $this->dns_cache);    
        
        $result = curl_exec($ch);        
        if (curl_errno($ch)) {
        	return $this->error_response('cURL Error '.curl_errno($ch), curl_error($ch));
        }

        $json = new \stdClass();
        $json->http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                        
        $response = json_decode($result); 
        if ($response) {
            $json->response = $response;        	
        } else {
        	$error = $this->error_response('Malformed JSON', $result);
         	$json = (object) array_merge((array) $json, (array) $error);
        }
        
        return $json;
    }
}
}?>