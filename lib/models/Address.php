<?php namespace Riskified\SDK {
    /**
     * Class Address
     * @package Riskified\SDK
     */
    class Address extends AbstractModel {

        protected $_fields = [
            'first_name' => 'string',
            'last_name' => 'string',
            'name' => 'string optional',
            'company' => 'string',
            'address1' => 'string',
            'address2' => 'string optional',
            'city' => 'string',
            'country_code' => 'string /^[A-Z]{2}$/i',
            'country' => 'string',
            'province_code' => 'string /^[A-Z]{2}$/i optional',
            'province' => 'string optional',
            'zip' => 'string optional',
            'phone' => 'string'
        ];
    }
}