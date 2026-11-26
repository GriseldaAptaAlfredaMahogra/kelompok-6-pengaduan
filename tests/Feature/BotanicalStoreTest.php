<?php

namespace Tests\Feature;

use App\Models\Bouquet;
use App\Models\Flower;
use App\Models\FlowerBatch;
use App\Models\OrderReview;
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
        $this->get(route('admin.reviews.index'))->assertOk()->assertSee('Ulasan Pembeli');
    }

    public function test_public_review_page_shows_verified_reviews_without_buyer_details(): void
    {
        $customer = $this->createCustomer('Alya Putri');
        $deliveredOrder = $this->createDeliveredOrder($customer);
        $deliveredOrder->items()->create([
            'item_name' => 'Garden Rose',
            'quantity' => 1,
            'unit_price' => 250000,
            'line_total' => 250000,
        ]);
        $deliveredOrder->review()->create([
            'rating' => 5,
            'comment' => "Buket <script>alert('x')</script> cantik dan tiba tepat waktu.",
        ]);

        $pendingOrder = $this->createDeliveredOrder($customer, 'pending');
        $pendingOrder->review()->create([
            'rating' => 1,
            'comment' => 'Ulasan pesanan yang belum diterima.',
        ]);
        auth()->logout();

        $this->get(route('shop.reviews.index'))
            ->assertOk()
            ->assertSee(route('shop.reviews.index'), false)
            ->assertSee('Pembeli terverifikasi')
            ->assertSee('Garden Rose')
            ->assertSee('Buket &lt;script&gt;alert(&#039;x&#039;)&lt;/script&gt; cantik dan tiba tepat waktu.', false)
            ->assertDontSee("<script>alert('x')</script>", false)
            ->assertDontSee($customer->email)
            ->assertDontSee('Ulasan pesanan yang belum diterima.')
            ->assertSee('1 ulasan terverifikasi');
    }

    public function test_admin_pages_require_a_signed_in_user(): void
    {
        auth()->logout();

        $this->get(route('admin.reviews.index'))->assertRedirect(route('login'));
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
            ->assertSee('Selesai & Terkirim')
            ->assertSee('tidak terhubung ke akun pelanggan');
        $this->assertNotNull($order->fresh()->completed_at);
    }

    public function test_buyer_can_submit_one_review_for_their_delivered_order_and_admin_can_view_statistics(): void
    {
        $admin = auth()->user();
        $customer = $this->createCustomer();
        $order = $this->createDeliveredOrder($customer);
        $reviewUrl = route('reviews.create', ['order' => $order]);

        $this->actingAs($customer)
            ->get(route('customer.reviews.pending'))
            ->assertRedirect($reviewUrl);

        $this->actingAs($customer)
            ->get(route('customer.orders.show', $order))
            ->assertOk()
            ->assertSee('Beri Ulasan')
            ->assertSee($reviewUrl, false);
        $this->get(route('customer.orders.index'))->assertOk()->assertSee('Beri Ulasan');
        $this->get($reviewUrl)->assertOk()->assertSee('Bagaimana rangkaian bunga Anda?');
        $this->post(route('reviews.store', ['order' => $order]), [
            'rating' => 5,
            'comment' => "Buket <script>alert('x')</script> cantik dan tiba tepat waktu.",
        ])->assertRedirect(route('customer.orders.show', $order));

        $this->assertDatabaseHas('order_reviews', [
            'order_id' => $order->id,
            'rating' => 5,
            'comment' => "Buket <script>alert('x')</script> cantik dan tiba tepat waktu.",
        ]);

        $this->get(route('customer.orders.show', $order))
            ->assertOk()
            ->assertDontSee(route('reviews.create', $order), false)
            ->assertSee('Buket &lt;script&gt;alert(&#039;x&#039;)&lt;/script&gt; cantik dan tiba tepat waktu.', false);
        $this->get(route('customer.orders.index'))
            ->assertOk()
            ->assertDontSee(route('reviews.create', $order), false);

        $this->get(route('customer.reviews.pending'))
            ->assertRedirect(route('customer.orders.index'))
            ->assertSessionHas('warning', 'Tidak ada pesanan yang menunggu ulasan.');

        $this->post(route('reviews.store', ['order' => $order]), [
            'rating' => 1,
            'comment' => 'Ulasan ganda tidak boleh mengganti ulasan pertama.',
        ])->assertRedirect(route('customer.orders.show', $order));
        $this->assertSame(1, OrderReview::where('order_id', $order->id)->count());
        $this->assertSame(
            "Buket <script>alert('x')</script> cantik dan tiba tepat waktu.",
            OrderReview::where('order_id', $order->id)->value('comment')
        );

        $secondCustomer = $this->createCustomer('Nadia Putri');
        $secondOrder = $this->createDeliveredOrder($secondCustomer);
        $secondOrder->review()->create([
            'rating' => 3,
            'comment' => 'Rangkaian tiba dengan indah.',
        ]);

        $this->actingAs($admin)->get(route('admin.reviews.index'))
            ->assertOk()
            ->assertSee('Buket &lt;script&gt;alert(&#039;x&#039;)&lt;/script&gt; cantik dan tiba tepat waktu.', false)
            ->assertDontSee("<script>alert('x')</script>", false)
            ->assertSee('4,0')
            ->assertSee('Dari 2 ulasan pembeli')
            ->assertSee('Alya Putri')
            ->assertSee('Nadia Putri')
            ->assertSee('Rangkaian tiba dengan indah.');

        $this->get(route('admin.reviews.index', ['rating' => 1]))
            ->assertOk()
            ->assertSee('Belum ada ulasan');
    }

    public function test_review_routes_require_login_and_only_allow_the_order_owner(): void
    {
        $customer = $this->createCustomer();
        $otherCustomer = $this->createCustomer();
        $order = $this->createDeliveredOrder($customer);
        $reviewUrl = route('reviews.create', ['order' => $order]);
        $submissionUrl = route('reviews.store', ['order' => $order]);

        auth()->logout();
        $this->get($reviewUrl)->assertRedirect(route('login'));
        $this->post($submissionUrl, [
            'rating' => 5,
            'comment' => 'Ulasan tanpa autentikasi tidak boleh tersimpan.',
        ])->assertRedirect(route('login'));

        $this->actingAs($otherCustomer)->get($reviewUrl)->assertNotFound();
        $this->post($submissionUrl, [
            'rating' => 5,
            'comment' => 'Ulasan untuk pesanan pelanggan lain tidak boleh tersimpan.',
        ])->assertNotFound();

        $this->assertDatabaseCount('order_reviews', 0);
    }

    public function test_customer_cannot_review_an_order_before_it_is_delivered(): void
    {
        $customer = $this->createCustomer();
        $order = $this->createDeliveredOrder($customer, 'pending');
        $reviewUrl = route('reviews.create', ['order' => $order]);

        $this->actingAs($customer)
            ->get(route('customer.orders.show', $order))
            ->assertOk()
            ->assertDontSee(route('reviews.create', $order), false);
        $this->get($reviewUrl)->assertNotFound();
        $this->post(route('reviews.store', ['order' => $order]), [
            'rating' => 5,
            'comment' => 'Pesanan ini belum selesai diterima.',
        ])->assertNotFound();

        $this->assertDatabaseCount('order_reviews', 0);
    }

    public function test_invalid_rating_and_comment_are_not_saved(): void
    {
        $customer = $this->createCustomer();
        $order = $this->createDeliveredOrder($customer);
        $reviewUrl = route('reviews.create', ['order' => $order]);

        $this->actingAs($customer)->from($reviewUrl)->post(route('reviews.store', ['order' => $order]), [
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

    private function createCustomer(string $name = 'Alya Putri'): User
    {
        $customer = User::factory()->create(['name' => $name]);
        $customer->role = 'customer';
        $customer->save();

        return $customer;
    }

    private function createDeliveredOrder(User $customer, string $status = 'delivered'): StoreOrder
    {
        return $customer->orders()->create([
            'order_number' => 'BA-REVIEW-'.fake()->unique()->numerify('#####'),
            'customer_name' => 'Sarah Kinanti',
            'phone' => '+62 812 0000 0000',
            'status' => $status,
            'total' => 250000,
            'completed_at' => $status === 'delivered' ? now() : null,
        ]);
    }
}
