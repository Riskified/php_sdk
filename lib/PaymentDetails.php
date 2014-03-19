<?php

class PaymentDetails extends Base{

  /**
   * The PaymentDetails class should contain the following fields:
   * credit_card_bin
   * avs_result_code
   * cvv_result_code
   * credit_card_number
   * credit_card_company
   */
     
    protected $creditCardBin;
    protected $avsResultCode;
    protected $cvvResultCode;
    protected $creditCardNumber;
    protected $creditCardCompany;
    
    public function setCreditCardBin($creditCardBin) {
        $this->creditCardBin = $creditCardBin;
    }
    public function setAvsResultCode($avsResultCode) {
        $this->avsResultCode = $avsResultCode;
    }
    public function setCvvResultCode($cvvResultCode) {
        $this->cvvResultCode = $cvvResultCode;
    }
    public function setCreditCardNumber($creditCardNumber) {
        $this->creditCardNumber = $creditCardNumber;
    }
    public function setCreditCardCompany($creditCardCompany) {
        $this->creditCardCompany = $creditCardCompany;
    }
    
    public function getCreditCardBin() {
        return $this->creditCardBin;
    }
    public function getAvsResultCode() {
        return $this->avsResultCode;
    }
    public function getCvvResultCode() {
        return $this->cvvResultCode;
    }
    public function getCreditCardNumber() {
        return $this->creditCardNumber;
    }
    public function getCreditCardCompany() {
        return $this->creditCardCompany;
    }
    
    
    
    
}

