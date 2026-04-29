<?php

namespace Riskified\Tests\OrderWebhook\Model;

use Riskified\OrderWebhook\Model\Customer;
use Riskified\OrderWebhook\Model\LineItem;

final class OrderTestFixtures
{
    /**
     * @return array<string, mixed>
     */
    public static function minimalOrderProps(): array
    {
        return [
            'id' => 'order-1',
            'email' => 'buyer@example.com',
            'created_at' => '2024-01-01T00:00:00+00:00',
            'updated_at' => '2024-01-01T00:00:00+00:00',
            'currency' => 'USD',
            'gateway' => 'test',
            'total_price' => 10.0,
            'browser_ip' => '127.0.0.1',
            'source' => 'web',
            'customer' => new Customer([
                'email' => 'buyer@example.com',
                'first_name' => 'Test',
                'last_name' => 'User',
            ]),
            'line_items' => [
                new LineItem([
                    'price' => 10.0,
                    'quantity' => '1',
                    'title' => 'Item',
                ]),
            ],
        ];
    }
}
