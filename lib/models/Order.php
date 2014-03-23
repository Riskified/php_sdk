<?php namespace Riskified\SDK {
    class Order extends AbstractModel {

        protected $_fields = [
            'id' => 'string',
            'name' => 'string optional',
            'email' => 'string',
            'total_spent' => 'number optional',
            'cancel_reason' => 'string optional',
            'cancelled_at' => 'date optional',
            'created_at' => 'date',
            'closed_at' => 'date optional',
            'currency' => 'string',
            'updated_at' => 'date',
            'gateway' => 'string',
            'browser_ip' => 'string',
            'cart_token' => 'string optional',
            'note' => 'string optional',
            'referring_site' => 'string optional',
            'total_price' => 'float',
            'total_discounts' => 'float optional',
            'customer' => 'object',
            'shipping_address' => 'object',
            'billing_address' => 'object',
            'payment_details' => 'object',
            'line_items' => 'objects',
            'discount_codes' => 'objects optional',
            'shipping_lines' => 'objects optional'
        ];
    }
}