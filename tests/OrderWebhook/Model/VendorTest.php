<?php

namespace Riskified\Tests\OrderWebhook\Model;

use PHPUnit\Framework\TestCase;
use Riskified\OrderWebhook\Model\Vendor;

class VendorTest extends TestCase {
    public function testDocumentedFieldsSerialize(): void {
        $vendor = new Vendor(['vendor_id' => 'VNDR7890', 'vendor_name' => 'Example Partner']);

        $this->assertTrue($vendor->validate(false));
        $this->assertSame('{"vendor_id":"VNDR7890","vendor_name":"Example Partner"}', $vendor->toJson());
    }

    public function testEveryFieldIsOptional(): void {
        $this->assertTrue((new Vendor())->validate(true));
    }
}
