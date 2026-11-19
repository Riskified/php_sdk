<?php

namespace Riskified\Tests\OrderWebhook\Model;

use PHPUnit\Framework\TestCase;
use Riskified\Common\Riskified;
use Riskified\OrderWebhook\Model\AbstractModel;
use Riskified\OrderWebhook\Model\Order;

class AbstractModelWireTransformTest extends TestCase {
    protected function tearDown(): void {
        Riskified::$legacy_wire_format = false;
    }

    private static function renamingModel(array $props): AbstractModel {
        return new class ($props) extends AbstractModel {
            protected $_fields = ['name' => 'string optional', 'child' => 'object optional'];

            protected function wire_transform($data) {
                if (isset($data['name'])) {
                    $data['wire_name'] = $data['name'];
                    unset($data['name']);
                }
                return $data;
            }
        };
    }

    public function testModelsWithoutAHookSerializeUnchanged(): void {
        $this->assertSame('{"id":"order-1"}', (new Order(['id' => 'order-1']))->toJson());
    }

    public function testTheHookRunsOnTheModelAndOnNestedModels(): void {
        $model = self::renamingModel(['name' => 'parent', 'child' => self::renamingModel(['name' => 'child'])]);

        $this->assertSame('{"child":{"wire_name":"child"},"wire_name":"parent"}', $model->toJson());
    }

    public function testTheLegacySwitchSkipsTheHook(): void {
        Riskified::$legacy_wire_format = true;
        $model = self::renamingModel(['name' => 'parent', 'child' => self::renamingModel(['name' => 'child'])]);

        $this->assertSame('{"name":"parent","child":{"name":"child"}}', $model->toJson());
    }

    public function testTheHookAlsoShapesXml(): void {
        $this->assertStringContainsString('<wire_name>x</wire_name>', self::renamingModel(['name' => 'x'])->toXml());
    }
}
