<?php namespace riskified\sdk {
class CurlTransport extends Transport {
        
    protected function sendJsonRequest($order) {   
		$dataString = $order->to_json();
		$hmac = $this->calc_hmac($dataString);   
		$headers = array(
			'Content-Type: application/json',
			'Content-Length: ' . strlen($dataString),
			'X_RISKIFIED_SHOP_DOMAIN:'.$this->domain,
			'X_RISKIFIED_SUBMIT_NOW:true',
			'X_RISKIFIED_HMAC_SHA256:'.$hmac
		);
		
        $ch = curl_init("http://$this->url/webhooks/merchant_order_created");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);    
        $result = curl_exec($ch);
        
        return json_decode($result);
    }
}
}?>