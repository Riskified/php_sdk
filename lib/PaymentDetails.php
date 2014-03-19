<?php

class PaymentDetails extends Base{

  /**
   * 
   * REQUIRED 
   *
   * The PaymentDetails class should contain the following fields:
   * credit_card_bin
   * avs_result_code
   * cvv_result_code
   * credit_card_number
   * credit_card_company
   */
     
    protected $credit_card_bin;       #required - string
    protected $avs_result_code;       #required - string
    protected $cvv_result_code;       #required - string
    protected $credit_card_number;    #required - string
    protected $credit_card_company;   #required - string
    
    public function setCreditCardBin($creditCardBin) {
        if(!$creditCardBin)
            throw new Exception('The PaymentDetails - CreditCardBin field is required');
        else   
            $this->credit_card_bin = $creditCardBin;
    }
    
    public function setAvsResultCode($avsResultCode) {
        if(!$avsResultCode)
            throw new Exception('The PaymentDetails - AvsResultCode field is required');
        else    
            $this->avs_result_code = $avsResultCode;
    }
    
    public function setCvvResultCode($cvvResultCode) {
        if(!$cvvResultCode)
            throw new Exception('The PaymentDetails - CvvResultCode field is required');
        else    
            $this->cvv_result_code = $cvvResultCode;
    }
    
    public function setCreditCardNumber($creditCardNumber) {
        if(!$creditCardNumber)
            throw new Exception('The PaymentDetails - CreditCardNumber field is required');
        else
            $this->credit_card_number = $creditCardNumber;
    }
    
    public function setCreditCardCompany($creditCardCompany) {
        if(!$creditCardCompany)
            throw new Exception('The PaymentDetails - CreditCardCompany field is required');
        else        
            $this->credit_card_company = $creditCardCompany;
    }
    
    public function getCreditCardBin() {
        return $this->credit_card_bin;
    }
    public function getAvsResultCode() {
        return $this->avs_result_code;
    }
    public function getCvvResultCode() {
        return $this->cvv_result_code;
    }
    public function getCreditCardNumber() {
        return $this->credit_card_number;
    }
    public function getCreditCardCompany() {
        return $this->credit_card_company;
    }
    
    
    
    
}

