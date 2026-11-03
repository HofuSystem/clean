<?php

namespace Tests\Unit\Audit;

use Core\Orders\Models\OrderItem;
use Core\Orders\Services\OrdersService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Tests\Support\IsolatedAuditTestCase;

class OrderItemDeletionTest extends IsolatedAuditTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->text('product_data')->nullable();
            $table->unsignedInteger('quantity')->default(0);
            $table->boolean('final_delete')->default(false);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    private function service(): OrdersService
    {
        return new class extends OrdersService {
            public array $recalculated = [];

            public function __construct() {}

            public function reCalcOrder($id)
            {
                $this->recalculated[] = $id;
            }
        };
    }

    public function test_initial_delete_remains_visible_but_final_delete_disappears_from_order_without_erasing_record(): void
    {
        $id = DB::table('order_items')->insertGetId([
            'order_id' => 12,
            'product_data' => '{"type":"clothes"}',
            'quantity' => 0,
            'final_delete' => false,
        ]);
        $service = $this->service();

        OrderItem::withoutEvents(fn () => $service->destroyItem(12, $id));
        $first = OrderItem::withTrashed()->findOrFail($id);
        $this->assertNotNull($first->deleted_at);
        $this->assertFalse((bool) $first->final_delete);
        $this->assertSame([$id], OrderItem::where('order_id', 12)->visibleInOrder()->pluck('id')->all());

        OrderItem::withoutEvents(fn () => $service->destroyItem(12, $id, true));
        $final = OrderItem::withTrashed()->findOrFail($id);
        $this->assertTrue((bool) $final->final_delete);
        $this->assertEquals($first->deleted_at, $final->deleted_at);
        $this->assertCount(0, OrderItem::where('order_id', 12)->visibleInOrder()->get());
        $this->assertSame([12], $service->recalculated);

        OrderItem::withoutEvents(fn () => $service->destroyItem(12, $id, true));
        $this->assertSame([12], $service->recalculated);
    }

    public function test_final_delete_requires_initial_delete(): void
    {
        $id = DB::table('order_items')->insertGetId(['order_id' => 12]);

        try {
            $this->service()->destroyItem(12, $id, true);
            $this->fail('Final deletion of an active item should be rejected.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('item', $exception->errors());
        }

        $this->assertNull(DB::table('order_items')->where('id', $id)->value('deleted_at'));
        $this->assertSame(0, DB::table('order_items')->where('id', $id)->value('final_delete'));
    }

    public function test_item_cannot_be_deleted_through_another_orders_url(): void
    {
        $id = DB::table('order_items')->insertGetId(['order_id' => 12]);

        $this->expectException(ModelNotFoundException::class);
        $this->service()->destroyItem(13, $id);
    }
}
