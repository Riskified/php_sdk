<?php

namespace Riskified\Tests\OrderWebhook\Transport;

use Riskified\OrderWebhook\Transport\AbstractTransport;

/**
 * A transport that records the absolute URL every request would have been sent to,
 * instead of sending it. It exercises the real host-resolution path in
 * AbstractTransport (endpoint_prefix) without touching the network.
 */
final class RecordingTransport extends AbstractTransport {
    /** @var array<int, string> */
    public array $requestedUrls = [];

    protected function send_json_request($json, $endpoint) {
        $this->requestedUrls[] = $this->endpoint_prefix() . $endpoint;

        return null;
    }

    protected function send_account_json_request($json, $endpoint) {
        $this->requestedUrls[] = $this->endpoint_prefix('customers') . $endpoint;

        return null;
    }
}
