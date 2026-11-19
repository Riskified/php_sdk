<?php

namespace Riskified\Tests\OrderWebhook\Model;

use PHPUnit\Framework\TestCase;
use Riskified\OrderWebhook\Model\LineItem;
use Riskified\OrderWebhook\Model\Order;

class AbstractModelXmlTest extends TestCase {
    public function testListItemsAreNumberedAndTheXmlParses(): void {
        $order = new Order([
            'line_items' => [new LineItem(['title' => 'A']), new LineItem(['title' => 'B'])],
        ]);

        $xml = $order->toXml();

        $this->assertStringContainsString(
            '<line_items><item0><title>A</title></item0><item1><title>B</title></item1></line_items>',
            $xml
        );
        $this->assertNotFalse(simplexml_load_string($xml), 'toXml() must produce well-formed XML');
    }

    public function testSpecialCharactersAreEscaped(): void {
        $order = new Order(['id' => 'O\'Brien "x" <b> & co']);

        $this->assertStringContainsString("<id>O'Brien \"x\" &lt;b&gt; &amp; co</id>", $order->toXml());
    }

    /**
     * PHP 8.1 changed htmlspecialchars()' default flags; before it, an invalid UTF-8 value became empty.
     */
    public function testInvalidUtf8IsReplacedNotDropped(): void {
        $order = new Order(['note' => "bad\xC3\x28utf8"]);

        $this->assertStringContainsString('<note>bad&#xFFFD;(utf8</note>', $order->toXml());
    }
}
