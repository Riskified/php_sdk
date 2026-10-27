<?php

/**
 * Copyright 2013-2026 Riskified.com, Inc. or its affiliates. All Rights Reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may not use this file except in compliance with the License.
 * A copy of the License is located at
 *
 * http://www.apache.org/licenses/LICENSE-2.0.html
 *
 * or in the "license" file accompanying this file. This file is distributed
 * on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either
 * express or implied. See the License for the specific language governing
 * permissions and limitations under the License.
 */

namespace Riskified\DecisionNotification\Exception;

use Riskified\Common\Exception\BaseException;
use Riskified\Common\Signature\HttpDataSignature;

/**
 * Class NotificationException
 * Base exception for issues with the Notification Model
 * @package Riskified\DecisionNotification\Exception
 */
class NotificationException extends BaseException {
    protected $headers;
    protected $body;

    public function __construct($headers, $body) {
        $this->headers = $headers;
        $this->body = $body;
        parent::__construct($this->customMessage());
    }

    protected function headersString() {
        $headers = $this->headers;
        $hmacKey = HttpDataSignature::HMAC_HEADER_NAME;
        if (isset($headers[$hmacKey])) {
            $headers[$hmacKey] = '***' . substr($headers[$hmacKey], -3);
        }
        return '[ ' . join(', ', $headers) . ' ]';
    }

    protected function customMessage() {
        return 'Headers: ' . $this->headersString() .
        ', Body length: ' . strlen((string) $this->body);
    }

    /**
     * The raw request body that triggered this exception.
     *
     * Deliberately absent from getMessage(). The body is attacker-controlled, and exception
     * messages routinely reach HTTP responses and log aggregators; echoing it back is an
     * information-disclosure and log-injection channel. Read it here once you have decided
     * where the body is safe to send.
     *
     * @return string The unmodified request body
     */
    public function getBody() {
        return $this->body;
    }

    /**
     * The request headers that triggered this exception, exactly as passed in.
     *
     * Unmasked, unlike headersString(), which is what getMessage() uses. Treat the result as
     * sensitive and do not return it to the caller of your webhook endpoint.
     *
     * @return array The unmodified request headers
     */
    public function getHeaders() {
        return $this->headers;
    }
}
