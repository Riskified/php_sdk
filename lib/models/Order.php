<?php namespace Riskified\SDK {
    class Order extends AbstractModel {

        protected $_fields = [
            'id' => 'string',
            'name' => 'string optional',
            'email' => 'string /^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i',
            'total_spent' => 'number optional',
            'cancel_reason' => 'string optional',
            'cancelled_at' => 'date optional',
            'created_at' => 'date',
            'closed_at' => 'date optional',
            'currency' => 'string',
            'updated_at' => 'date',
            'gateway' => 'string',
            'browser_ip' => 'string /^(\d{1,3}\.){3}\d{1,3}$/',
            'cart_token' => 'string optional',
            'note' => 'string optional',
            'referring_site' => 'string optional',
            'total_price' => 'float',
            'total_discounts' => 'float optional',
            'customer' => 'object \Customer',
            'shipping_address' => 'object \Address',
            'billing_address' => 'object \Address',
            'payment_details' => 'object \PaymentDetails',
            'line_items' => 'objects \LineItem',
            'discount_codes' => 'objects \DiscountCode optional',
            'shipping_lines' => 'objects \ShippingLine optional'
        ];
    }
}