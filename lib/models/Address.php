<?php namespace Riskified\SDK {
    class Address extends AbstractModel {

        protected $_fields = [
            'first_name' => 'string',
            'last_name' => 'string',
            'name' => 'string optional',
            'company' => 'string',
            'address1' => 'string',
            'address2' => 'string optional',
            'city' => 'string',
            'country_code' => 'string',
            'country' => 'string',
            'province_code' => 'string optional',
            'province' => 'string optional',
            'zip' => 'string optional',
            'phone' => 'string'
        ];
    }
}