<?php

class Address extends Base{

  /**
   * The address class should contain the following fields:
   * - first_name
   * - last_name
   * - name
   * - company
   * - address_1 
   * - address_2
   * - city
   * - country_code (2 letter country code)
   * - country
   * - province
   * - province_code
   * - zip
   * - phone
   */
  
    protected $firstName;
    protected $lastName;
    protected $name;
    protected $company;
    protected $addressOne;
    protected $addressTwo;
    protected $city;
    protected $countryCode;
    protected $country;
    protected $province;
    protected $provinceCode;
    protected $zip;
    protected $phone;
    
    public function setFirstName($firstName){
        $this->firstName = $firstName;
    }
    public function setLastName($lastName){
        $this->lastName = $lastName;
    }
    public function setName($name){
        $this->name = $name;
    }
    public function setCompany($company){
        $this->company = $company;
    }
    public function setAddressOne($addressOne){
        $this->addressOne = $addressOne;
    }
    public function setAddressTwo($addressTwo){
        $this->addressTwo = $addressTwo;
    }
    public function setCity($city){
        $this->city = $city;
    }
    public function setCountryCode($countryCode){
        $this->countryCode = $countryCode;
    }
    public function setCountry($country){
        $this->country = $country;
    }
    public function setProvince($province){
        $this->province = $province;
    }
    public function setProvinceCode($provinceCode){
        $this->provinceCode = $provinceCode;
    }
    public function setZip($zip){
        $this->zip = $zip;
    }
    public function setPhone($phone){
        $this->phone = $phone;
    }
    
    
    public function getFirstName(){
        return $this->firstName;
    }
    
    public function getLastName(){
        return $this->lastName;
    }
    
    public function getName(){
        return $this->name;
    }
    
    public function getFirsName(){
        return $this->firstName;
    }
    
    public function getCompany(){
        return $this->company;
    }
    
    public function getAddressOne(){
        return $this->addressOne;
    }
    
    public function getAddressTwo(){
        return $this->addressTwo;
    }
    
    public function getCity(){
        return $this->city;
    }
    
    public function getCountryCode(){
        return $this->countryCode;
    }
    
    public function getCountry(){
        return $this->country;
    }
    
    public function getProvince(){
        return $this->province;
    }
    
    public function getProvinceCode(){
        return $this->provinceCode;
    }
    public function getZip(){
        return $this->zip;
    }
    public function getPhone(){
        return $this->phone;
    }
    
    
  
}

