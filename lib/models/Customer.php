<?php namespace Riskified\SDK {
    class Customer extends AbstractModel {

        protected $_fields = [
            'created_at' => 'date optional',
            'email' => 'string',
            'first_name' => 'string',
            'last_name' => 'string',
            'id' => 'string',
            'note' => 'string optional',
            'orders_count' => 'number optional',
            'verified_email' => 'boolean optional',
        ];
    }
}