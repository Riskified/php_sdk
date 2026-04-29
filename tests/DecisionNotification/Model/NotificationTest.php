<?php

namespace Riskified\Tests\DecisionNotification\Model;

use PHPUnit\Framework\TestCase;
use Riskified\Common\Riskified;
use Riskified\Common\Signature\HttpDataSignature;
use Riskified\DecisionNotification\Exception\AuthorizationException;
use Riskified\DecisionNotification\Exception\BadPostJsonException;
use Riskified\DecisionNotification\Model\Notification;

/**
 * Regression tests for {@see Notification} parsing and HMAC validation.
 */
final class NotificationTest extends TestCase
{
    private function signature(): NotificationTestSignature
    {
        return new NotificationTestSignature();
    }

    /**
     * @param array<string, string> $extraHeaders
     * @return array<string, string>
     */
    private function authorizedHeadersForBody(string $body, array $extraHeaders = []): array
    {
        $sig = $this->signature();

        return $extraHeaders + [
            $sig::HMAC_HEADER_NAME => $sig->calc_hmac($body),
        ];
    }

    public function testParsesFullOrderPayloadAndMapsSnakeCaseFields(): void
    {
        $body = <<<'JSON'
{
  "order": {
    "id": "ord-100",
    "status": "approved",
    "old_status": "pending",
    "description": "low risk",
    "category": "default",
    "risk_score": 12,
    "risk_indicators": [{"code": "a"}],
    "decision_code": "DC-1"
  }
}
JSON;

        $notification = new Notification(
            $this->signature(),
            $this->authorizedHeadersForBody($body),
            $body
        );

        $this->assertSame('ord-100', $notification->id);
        $this->assertSame('approved', $notification->status);
        $this->assertSame('pending', $notification->oldStatus);
        $this->assertSame('low risk', $notification->description);
        $this->assertSame('default', $notification->category);
        $this->assertSame(12, $notification->riskScore);
        $this->assertSame([['code' => 'a']], $notification->riskIndicators);
        $this->assertSame('DC-1', $notification->decisionCode);
    }

    public function testParsesMinimalRequiredFields(): void
    {
        $body = '{"order":{"id":"x","status":"cancelled","old_status":"pending","description":null}}';

        $notification = new Notification(
            $this->signature(),
            $this->authorizedHeadersForBody($body),
            $body
        );

        $this->assertSame('x', $notification->id);
        $this->assertSame('cancelled', $notification->status);
        $this->assertSame('pending', $notification->oldStatus);
        $this->assertNull($notification->description);
    }

    public function testMissingRiskScoreDefaultsToZero(): void
    {
        $body = '{"order":{"id":"182","status":"s","old_status":"o","description":null}}';

        $notification = new Notification(
            $this->signature(),
            $this->authorizedHeadersForBody($body),
            $body
        );

        $this->assertSame(0, $notification->riskScore);
    }

    public function testNullRiskScoreCoalescesToZero(): void
    {
        $body = '{"order":{"id":"1","status":"s","old_status":"o","description":null,"risk_score":null}}';

        $notification = new Notification(
            $this->signature(),
            $this->authorizedHeadersForBody($body),
            $body
        );

        $this->assertSame(0, $notification->riskScore);
    }

    public function testMissingRiskIndicatorsDefaultsToEmptyArray(): void
    {
        $body = '{"order":{"id":"1","status":"s","old_status":"o","description":null}}';

        $notification = new Notification(
            $this->signature(),
            $this->authorizedHeadersForBody($body),
            $body
        );

        $this->assertSame([], $notification->riskIndicators);
    }

    public function testCategoryAndDecisionCodeUnsetWhenAbsentFromPayload(): void
    {
        $body = '{"order":{"id":"1","status":"s","old_status":"o","description":null}}';

        $notification = new Notification(
            $this->signature(),
            $this->authorizedHeadersForBody($body),
            $body
        );

        $this->assertNull($notification->category);
        $this->assertNull($notification->decisionCode);
    }

    public function testThrowsBadPostJsonWhenOrderKeyMissing(): void
    {
        $this->expectException(BadPostJsonException::class);

        $body = '{"other":true}';
        new Notification(
            $this->signature(),
            $this->authorizedHeadersForBody($body),
            $body
        );
    }

    public function testThrowsBadPostJsonWhenOrderIdMissing(): void
    {
        $this->expectException(BadPostJsonException::class);

        $body = '{"order":{"status":"approved","old_status":"pending"}}';
        new Notification(
            $this->signature(),
            $this->authorizedHeadersForBody($body),
            $body
        );
    }

    public function testThrowsBadPostJsonWhenOrderStatusMissing(): void
    {
        $this->expectException(BadPostJsonException::class);

        $body = '{"order":{"id":"1","old_status":"pending"}}';
        new Notification(
            $this->signature(),
            $this->authorizedHeadersForBody($body),
            $body
        );
    }

    public function testThrowsBadPostJsonWhenBodyIsNotValidJsonObject(): void
    {
        $this->expectException(BadPostJsonException::class);

        $body = 'not-json';
        new Notification(
            $this->signature(),
            $this->authorizedHeadersForBody($body),
            $body
        );
    }

    public function testThrowsAuthorizationExceptionWhenHmacMismatch(): void
    {
        $this->expectException(AuthorizationException::class);

        $body = '{"order":{"id":"1","status":"s","old_status":"o","description":null}}';
        $sig = $this->signature();

        new Notification(
            $sig,
            [
                $sig::HMAC_HEADER_NAME => 'wrong-hmac',
            ],
            $body
        );
    }

    public function testParsesUsingSdkHttpDataSignature(): void
    {
        $prevToken = Riskified::$auth_token;
        Riskified::$auth_token = 'live-callback-token-fixture';

        try {
            $body = '{"order":{"id":"ord-99","status":"submitted","old_status":"pending","description":""}}';
            $signature = new HttpDataSignature();
            $headers = [
                HttpDataSignature::HMAC_HEADER_NAME => $signature->calc_hmac($body),
            ];

            $notification = new Notification($signature, $headers, $body);

            $this->assertSame('ord-99', $notification->id);
            $this->assertSame('submitted', $notification->status);
        } finally {
            Riskified::$auth_token = $prevToken;
        }
    }
}

final class NotificationTestSignature
{
    public const HMAC_HEADER_NAME = 'X-RISKIFIED-HMAC-SHA256';

    public function __construct(private string $secret = 'notification-test-secret')
    {
    }

    public function calc_hmac(string $body): string
    {
        return hash_hmac('sha256', $body, $this->secret);
    }
}
