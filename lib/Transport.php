<?php
class Transport extends Base {
    
    protected $hashCode = NULL;
    protected $ch = NULL;
    
    public function sendRequest($dataString, $url, $domain, $authToken) {
            
        $this->hashCode = $this->getHashHmac($dataString, $authToken);   
        $this->ch = curl_init("http://$url/webhooks/merchant_order_created");
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, $dataString);
        curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, array(
                                                   'Content-Type: application/json',
                                                   'Content-Length: ' . strlen($dataString),
                                                   'X_RISKIFIED_SHOP_DOMAIN:'.$domain,
                                                   'X_RISKIFIED_SUBMIT_NOW:true',
                                                   'X_RISKIFIED_HMAC_SHA256:'.$this->hashCode)
                    );
        curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, true);
        
        $result = curl_exec($this->ch);
        
        return $result;
    }
}
