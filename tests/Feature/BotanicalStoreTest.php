<?php

namespace Tests\Feature;

use App\Models\Bouquet;
use App\Models\Flower;
use App\Models\FlowerBatch;
use App\Models\StoreOrder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BotanicalStoreTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->create());
    }

    public function test_dashboard_and_store_pages_are_available(): void
    {
        $this->get(route('admin.dashboard'))->assertOk()->assertSee('Selamat pagi, florist');
        $this->get(route('admin.bouquets.index'))->assertOk()->assertSee('Katalog & Rangkaian');
        $this->get(route('admin.inventory.index'))->assertOk()->assertSee('Katalog Stok Bunga Segar');
        $this->get(route('admin.suppliers.index'))->assertOk()->assertSee('Supplier Atelier');
    }

    public function test_admin_pages_require_a_signed_in_user(): void
    {
        auth()->logout();

        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
        $this->get(route('login'))->assertOk()->assertSee('Rangkaian pilihan');

        $user = User::firstOrFail();
        $this->post(route('login.store'), ['email' => $user->email, 'password' => 'password'])
            ->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_custom_order_can_be_created_and_marked_delivered(): void
    {
        $bouquet = Bouquet::create([
            'name' => 'The Luminary Pastel',
            'slug' => 'the-luminary-pastel',
            'category' => 'Buket Tangan',
            'price' => 735000,
            'is_active' => true,
        ]);
        $flower = Flower::create([
            'name' => 'Mawar',
            'variety' => 'Ekuador Pastel',
            'category' => 'Bunga Utama',
            'unit' => 'tangkai',
            'low_stock_threshold' => 10,
        ]);
        $batch = FlowerBatch::create([
            'flower_id' => $flower->id,
            'batch_code' => 'TEST-MAWAR-001',
            'quantity' => 5,
            'purchase_price' => 25000,
            'received_at' => now()->toDateString(),
            'expires_at' => now()->addDays(7)->toDateString(),
        ]);

        $response = $this->post(route('admin.orders.store'), [
            'customer_name' => 'Sarah Kinanti',
            'phone' => '+62 812 0000 0000',
            'occasion' => 'Wisuda',
            'bouquet_id' => $bouquet->id,
            'quantity' => 1,
            'flower_items' => [$flower->id => 2],
            'customization_notes' => 'Pita warna sage',
            'message_card' => 'Selamat atas kelulusanmu.',
        ]);

        $order = StoreOrder::with('items')->firstOrFail();
        $response->assertRedirect(route('admin.orders.show', $order));
        $this->assertSame(2, $order->items->count());
        $this->assertSame('Pita warna sage', $order->customization_notes);
        $this->assertSame('765000.00', $order->total);
        $this->assertSame(3, $batch->fresh()->quantity);

        $this->patch(route('admin.orders.status', $order), ['status' => 'delivered'])
            ->assertRedirect(route('admin.orders.complete', $order));
        $this->get(route('admin.orders.complete', $order))
            ->assertOk()
            ->assertSee('Pesanan Berhasil')
            ->assertSee('Selesai & Terkirim');
        $this->assertNotNull($order->fresh()->completed_at);
    }

    public function test_cancelling_an_order_returns_reserved_flowers_to_stock(): void
    {
        $bouquet = Bouquet::create([
            'name' => 'Garden Rose',
            'slug' => 'garden-rose',
            'category' => 'Buket Tangan',
            'price' => 500000,
            'is_active' => true,
        ]);
        $flower = Flower::create([
            'name' => 'Hydrangea',
            'variety' => 'Soft Pink',
            'category' => 'Bunga Utama',
            'unit' => 'tangkai',
            'low_stock_threshold' => 5,
        ]);
        $batch = FlowerBatch::create([
            'flower_id' => $flower->id,
            'batch_code' => 'TEST-HYD-001',
            'quantity' => 8,
            'purchase_price' => 30000,
        ]);

        $this->post(route('admin.orders.store'), [
            'customer_name' => 'Nadia Putri',
            'phone' => '+62 812 1234 5678',
            'bouquet_id' => $bouquet->id,
            'quantity' => 1,
            'flower_items' => [$flower->id => 3],
        ])->assertRedirect();

        $order = StoreOrder::firstOrFail();
        $this->assertSame(5, $batch->fresh()->quantity);

        $this->patch(route('admin.orders.status', $order), ['status' => 'cancelled'])
            ->assertRedirect(route('admin.orders.show', $order));

        $this->assertSame(8, $batch->fresh()->quantity);
        $this->assertNotNull($order->fresh()->stock_released_at);
    }
}
