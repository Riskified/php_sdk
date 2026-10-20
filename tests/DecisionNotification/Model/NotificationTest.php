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
final class NotificationTest extends TestCase {
    private function signature(): NotificationTestSignature {
        return new NotificationTestSignature();
    }

    /**
     * @param array<string, string> $extraHeaders
     * @return array<string, string>
     */
    private function authorizedHeadersForBody(string $body, array $extraHeaders = []): array {
        $sig = $this->signature();

        return $extraHeaders + [
            $sig::HMAC_HEADER_NAME => $sig->calc_hmac($body),
        ];
    }

    public function testParsesFullOrderPayloadAndMapsSnakeCaseFields(): void {
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

    public function testParsesMinimalRequiredFields(): void {
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

    public function testMissingRiskScoreDefaultsToZero(): void {
        $body = '{"order":{"id":"182","status":"s","old_status":"o","description":null}}';

        $notification = new Notification(
            $this->signature(),
            $this->authorizedHeadersForBody($body),
            $body
        );

        $this->assertSame(0, $notification->riskScore);
    }

    public function testNullRiskScoreCoalescesToZero(): void {
        $body = '{"order":{"id":"1","status":"s","old_status":"o","description":null,"risk_score":null}}';

        $notification = new Notification(
            $this->signature(),
            $this->authorizedHeadersForBody($body),
            $body
        );

        $this->assertSame(0, $notification->riskScore);
    }

    public function testMissingRiskIndicatorsDefaultsToEmptyArray(): void {
        $body = '{"order":{"id":"1","status":"s","old_status":"o","description":null}}';

        $notification = new Notification(
            $this->signature(),
            $this->authorizedHeadersForBody($body),
            $body
        );

        $this->assertSame([], $notification->riskIndicators);
    }

    public function testCategoryAndDecisionCodeUnsetWhenAbsentFromPayload(): void {
        $body = '{"order":{"id":"1","status":"s","old_status":"o","description":null}}';

        $notification = new Notification(
            $this->signature(),
            $this->authorizedHeadersForBody($body),
            $body
        );

        $this->assertNull($notification->category);
        $this->assertNull($notification->decisionCode);
    }

    public function testThrowsBadPostJsonWhenOrderKeyMissing(): void {
        $this->expectException(BadPostJsonException::class);

        $body = '{"other":true}';
        new Notification(
            $this->signature(),
            $this->authorizedHeadersForBody($body),
            $body
        );
    }

    public function testThrowsBadPostJsonWhenOrderIdMissing(): void {
        $this->expectException(BadPostJsonException::class);

        $body = '{"order":{"status":"approved","old_status":"pending"}}';
        new Notification(
            $this->signature(),
            $this->authorizedHeadersForBody($body),
            $body
        );
    }

    public function testThrowsBadPostJsonWhenOrderStatusMissing(): void {
        $this->expectException(BadPostJsonException::class);

        $body = '{"order":{"id":"1","old_status":"pending"}}';
        new Notification(
            $this->signature(),
            $this->authorizedHeadersForBody($body),
            $body
        );
    }

    public function testThrowsBadPostJsonWhenBodyIsNotValidJsonObject(): void {
        $this->expectException(BadPostJsonException::class);

        $body = 'not-json';
        new Notification(
            $this->signature(),
            $this->authorizedHeadersForBody($body),
            $body
        );
    }

    public function testThrowsAuthorizationExceptionWhenHmacMismatch(): void {
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

    public function testAuthorizationExceptionMessageContainsMaskedHmacSuffix(): void {
        $body = '{"order":{"id":"1","status":"s","old_status":"o","description":null}}';
        $sig = $this->signature();
        $wrongHmac = 'abcdef1234567890abcdef1234567890abcdef1234567890abcdef1234567890';

        try {
            new Notification($sig, [$sig::HMAC_HEADER_NAME => $wrongHmac], $body);
            $this->fail('Expected AuthorizationException was not thrown');
        } catch (AuthorizationException $e) {
            $this->assertStringContainsString(
                '***' . substr($wrongHmac, -3),
                $e->getMessage(),
                'Masked HMAC (last 3 chars) must appear in exception message'
            );
        }
    }

    public function testParsesUsingSdkHttpDataSignature(): void {
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

    /**
     * SECURITY-10974. The exception message reaches HTTP responses and logs, so the
     * attacker-controlled request body must not be in it.
     */
    public function testAuthorizationExceptionMessageOmitsRequestBody(): void {
        $marker = 'forged-by-attacker-marker';
        $body = '{"order":{"id":"31337","status":"approved","description":"' . $marker . '"}}';
        $sig = $this->signature();

        try {
            new Notification($sig, [$sig::HMAC_HEADER_NAME => 'wrong-hmac'], $body);
            $this->fail('Expected AuthorizationException was not thrown');
        } catch (AuthorizationException $e) {
            $this->assertStringNotContainsString(
                $marker,
                $e->getMessage(),
                'Request body must not be reflected in the exception message'
            );
            $this->assertStringNotContainsString(
                $body,
                $e->getMessage(),
                'Request body must not be reflected in the exception message'
            );
        }
    }

    /**
     * SECURITY-10974. The computed HMAC is the value the original report exfiltrated; it must
     * never appear in the message, masked or otherwise.
     */
    public function testAuthorizationExceptionMessageOmitsComputedHmac(): void {
        $body = '{"order":{"id":"1","status":"s","old_status":"o","description":null}}';
        $sig = $this->signature();
        $computed = $sig->calc_hmac($body);

        try {
            new Notification($sig, [$sig::HMAC_HEADER_NAME => 'wrong-hmac'], $body);
            $this->fail('Expected AuthorizationException was not thrown');
        } catch (AuthorizationException $e) {
            $this->assertStringNotContainsString(
                $computed,
                $e->getMessage(),
                'Server-computed HMAC must never appear in the exception message'
            );
        }
    }

    /**
     * The body stays reachable for deliberate server-side logging - it is only the message
     * that must stay clean.
     */
    public function testAuthorizationExceptionStillExposesBodyViaAccessor(): void {
        $body = '{"order":{"id":"1","status":"s","old_status":"o","description":null}}';
        $sig = $this->signature();
        $headers = [$sig::HMAC_HEADER_NAME => 'wrong-hmac'];

        try {
            new Notification($sig, $headers, $body);
            $this->fail('Expected AuthorizationException was not thrown');
        } catch (AuthorizationException $e) {
            $this->assertSame($body, $e->getBody());
            $this->assertSame($headers, $e->getHeaders());
        }
    }

    public function testMissingHmacHeaderThrowsAuthorizationException(): void {
        $this->expectException(AuthorizationException::class);

        $body = '{"order":{"id":"1","status":"s","old_status":"o","description":null}}';

        new Notification($this->signature(), [], $body);
    }

    /**
     * A request with no signature must fail closed without tripping an undefined-key warning -
     * that warning discloses a filesystem path when display_errors is on.
     */
    public function testMissingHmacHeaderRaisesNoPhpWarning(): void {
        $body = '{"order":{"id":"1","status":"s","old_status":"o","description":null}}';

        set_error_handler(static function (int $severity, string $message): bool {
            throw new \RuntimeException('Unexpected PHP diagnostic: ' . $message);
        });

        try {
            new Notification($this->signature(), [], $body);
            $this->fail('Expected AuthorizationException was not thrown');
        } catch (AuthorizationException $e) {
            $this->addToAssertionCount(1);
        } finally {
            restore_error_handler();
        }
    }
}

// phpcs:ignore PSR1.Classes.ClassDeclaration.MultipleClasses -- test-only signature stub kept alongside its test
final class NotificationTestSignature {
    public const HMAC_HEADER_NAME = 'X-RISKIFIED-HMAC-SHA256';

    public function __construct(private string $secret = 'notification-test-secret') {
    }

    public function calc_hmac(string $body): string {
        return hash_hmac('sha256', $body, $this->secret);
    }
}
