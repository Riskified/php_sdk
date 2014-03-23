<?php namespace Riskified;
/**
 * Class Customer
 * @package Riskified
 * @property string $created_at
 */
class Customer extends AbstractModel {

    protected $_fields = [
        'created_at' => 'date optional',
        'email' => 'string /^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i',
        'first_name' => 'string',
        'last_name' => 'string',
        'id' => 'string',
        'note' => 'string optional',
        'orders_count' => 'number optional',
        'verified_email' => 'boolean optional',
    ];
}