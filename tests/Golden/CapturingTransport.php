<?php

namespace Riskified\Tests\Golden;

use Riskified\Common\Signature\HttpDataSignature;
use Riskified\OrderWebhook\Model\AbstractModel;
use Riskified\OrderWebhook\Transport\AbstractTransport;

/**
 * Transport that records the request instead of sending it, so tests see the exact wire payload.
 */
final class CapturingTransport extends AbstractTransport {
    public ?string $lastJson = null;
    public ?string $lastEndpoint = null;

    /**
     * Sends $model through the transport method a merchant would call and returns the recorded request.
     */
    public static function capture(string $method, AbstractModel $model): self {
        $transport = new self(new HttpDataSignature());
        $transport->$method($model);
        return $transport;
    }

    protected function send_json_request($json, $endpoint) {
        $this->lastJson = $json;
        $this->lastEndpoint = $endpoint;
        return null;
    }

    protected function send_account_json_request($json, $endpoint) {
        return $this->send_json_request($json, $endpoint);
    }
}
