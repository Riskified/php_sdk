<?php

class Address extends Base{

  /**
   * 
   * REQUIRED
   * 
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
  
    protected $first_name;   #required - string
    protected $last_name;    #required - string
    protected $name;        #optional - string
    protected $company;     #required - string - for billing   //\\    optional - string - for shipping
    protected $address1;  #required - string
    protected $address2;  #optional - string
    protected $city;        #required - string
    protected $country_code; #required - string
    protected $country;     #required - string
    protected $province;    #optional - string
    protected $province_code;#optional - string
    protected $zip;         #optional - string
    protected $phone;       #required - string
    
    public function setFirstName($firstName){
        if(!$firstName)
            throw new Exception('The - Address - First Name field is required.');
        else
        $this->first_name = $firstName;
    }
    public function setLastName($lastName){
        if(!$lastName)
            throw new Exception('The - Address - Last Name  field is required');
        else    
            $this->last_name = $lastName;
    }
    public function setName($name){
        $this->name = $name;
    }
    public function setCompany($company){
        $this->company = $company;
    }
    public function setAddressOne($addressOne){
        if(!$addressOne)
            throw new Exception('The - Address - Address 1  field is required');
        else
            $this->address1 = $addressOne;
    }
    public function setAddressTwo($addressTwo){
        $this->address2 = $addressTwo;
    }
    public function setCity($city){
        if(!$city)
            throw new Exception('The - Address - City  field is required');
        else
            $this->city = $city;
    }
    public function setCountryCode($countryCode){
        if(!$countryCode)
            throw new Exception('The - Address - Country Code  field is required');
        else
            $this->country_code = $countryCode;
    }
    public function setCountry($country){
        if(!$country)
            throw new Exception('The - Address - Country  field is required');
        else
            $this->country = $country;
    }
    public function setProvince($province){
        $this->province = $province;
    }
    public function setProvinceCode($provinceCode){
        $this->province_code = $provinceCode;
    }
    public function setZip($zip){
        $this->zip = $zip;
    }
    public function setPhone($phone){
        if(!$phone)
            throw new Exception('The - Address - Phone  field is required');
        else
            $this->phone = $phone;
    }
    
    
    public function getFirstName(){
        return $this->first_name;
    }
    
    public function getLastName(){
        return $this->last_name;
    }
    
    public function getName(){
        return $this->name;
    }
    
    public function getCompany(){
        return $this->company;
    }
    
    public function getAddressOne(){
        return $this->address1;
    }
    
    public function getAddressTwo(){
        return $this->address2;
    }
    
    public function getCity(){
        return $this->city;
    }
    
    public function getCountryCode(){
        return $this->country_code;
    }
    
    public function getCountry(){
        return $this->country;
    }
    
    public function getProvince(){
        return $this->province;
    }
    
    public function getProvinceCode(){
        return $this->province_code;
    }
    public function getZip(){
        return $this->zip;
    }
    public function getPhone(){
        return $this->phone;
    }
    
    
  
}

