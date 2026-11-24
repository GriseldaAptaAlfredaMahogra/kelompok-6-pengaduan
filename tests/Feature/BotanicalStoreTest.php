<?php

namespace Tests\Feature;

use App\Models\Bouquet;
use App\Models\Flower;
use App\Models\FlowerBatch;
use App\Models\OrderReview;
use App\Models\StoreOrder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
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
        $this->get(route('admin.reviews.index'))->assertOk()->assertSee('Ulasan Pembeli');
    }

    public function test_admin_pages_require_a_signed_in_user(): void
    {
        auth()->logout();

        $this->get(route('admin.reviews.index'))->assertRedirect(route('login'));
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
        $this->get(route('login'))->assertOk()->assertSee('Ruang merangkai');

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
            ->assertSee('Selesai & Terkirim')
            ->assertSee(route('reviews.create', ['order' => $order]), false);
        $this->assertNotNull($order->fresh()->completed_at);
    }

    public function test_buyer_can_submit_one_signed_review_and_admin_can_view_it(): void
    {
        $order = $this->createDeliveredOrder();
        $reviewUrl = URL::signedRoute('reviews.create', ['order' => $order]);

        $this->get($reviewUrl)->assertOk()->assertSee('Bagaimana rangkaian bunga Anda?');
        $this->post($reviewUrl, [
            'rating' => 5,
            'comment' => "Buket <script>alert('x')</script> cantik dan tiba tepat waktu.",
        ])->assertRedirect();

        $this->assertDatabaseHas('order_reviews', [
            'order_id' => $order->id,
            'rating' => 5,
            'comment' => "Buket <script>alert('x')</script> cantik dan tiba tepat waktu.",
        ]);

        $this->post($reviewUrl, [
            'rating' => 1,
            'comment' => 'Ulasan ganda tidak boleh mengganti ulasan pertama.',
        ])->assertRedirect();
        $this->assertDatabaseCount('order_reviews', 1);
        $this->assertSame(
            "Buket <script>alert('x')</script> cantik dan tiba tepat waktu.",
            OrderReview::where('order_id', $order->id)->value('comment')
        );

        $this->get(route('admin.reviews.index'))
            ->assertOk()
            ->assertSee('Buket &lt;script&gt;alert(&#039;x&#039;)&lt;/script&gt; cantik dan tiba tepat waktu.', false)
            ->assertDontSee("<script>alert('x')</script>", false)
            ->assertSee('5,0');

        $this->get(route('admin.reviews.index', ['rating' => 1]))
            ->assertOk()
            ->assertSee('Belum ada ulasan');
    }

    public function test_signed_review_link_rejects_tampering_and_orders_that_are_not_delivered(): void
    {
        $deliveredOrder = $this->createDeliveredOrder();
        $invalidUrl = str_replace(
            'signature=',
            'signature=invalid',
            URL::signedRoute('reviews.create', ['order' => $deliveredOrder])
        );
        $pendingOrder = $this->createDeliveredOrder('pending');
        $pendingUrl = URL::signedRoute('reviews.create', ['order' => $pendingOrder]);

        $this->get($invalidUrl)->assertForbidden();
        $this->get($pendingUrl)->assertNotFound();
        $this->post($pendingUrl, [
            'rating' => 5,
            'comment' => 'Pesanan ini belum selesai diterima.',
        ])->assertNotFound();

        $this->assertDatabaseCount('order_reviews', 0);
    }

    public function test_invalid_rating_and_comment_are_not_saved(): void
    {
        $order = $this->createDeliveredOrder();
        $reviewUrl = URL::signedRoute('reviews.create', ['order' => $order]);

        $this->from($reviewUrl)->post($reviewUrl, [
            'rating' => 6,
            'comment' => 'Oke',
        ])->assertSessionHasErrors(['rating', 'comment']);

        $this->assertDatabaseCount('order_reviews', 0);
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

    private function createDeliveredOrder(string $status = 'delivered'): StoreOrder
    {
        return StoreOrder::create([
            'order_number' => 'BA-REVIEW-'.fake()->unique()->numerify('#####'),
            'customer_name' => 'Sarah Kinanti',
            'phone' => '+62 812 0000 0000',
            'status' => $status,
            'total' => 250000,
            'completed_at' => $status === 'delivered' ? now() : null,
        ]);
    }
}
