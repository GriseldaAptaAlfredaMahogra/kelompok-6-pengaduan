<?php

namespace Tests\Feature;

use App\Models\Bouquet;
use App\Models\StoreOrder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthAndCustomerShoppingTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_registration_creates_a_hashed_customer_account(): void
    {
        $response = $this->post(route('register.store'), [
            'name' => 'Alya Putri',
            'email' => 'alya@example.test',
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
            'role' => 'admin',
        ]);

        $user = User::where('email', 'alya@example.test')->firstOrFail();

        $response->assertRedirect(route('shop.index'));
        $this->assertAuthenticatedAs($user);
        $this->assertSame('customer', $user->role);
        $this->assertTrue(Hash::check('rahasia123', $user->password));
        $this->assertNotSame('rahasia123', $user->password);
    }

    public function test_registration_rejects_an_email_that_already_exists(): void
    {
        User::factory()->create(['email' => 'alya@example.test']);

        $response = $this->post(route('register.store'), [
            'name' => 'Alya Putri',
            'email' => 'alya@example.test',
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
        $this->assertDatabaseCount('users', 1);
    }

    public function test_invalid_password_does_not_authenticate_a_user(): void
    {
        $user = User::factory()->create();

        $response = $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_customer_login_restores_a_guest_cart_and_continues_to_cart(): void
    {
        $bouquet = Bouquet::create([
            'name' => 'Pastel Garden',
            'slug' => 'pastel-garden',
            'category' => 'Buket Tangan',
            'price' => 450000,
            'is_active' => true,
        ]);
        $user = User::factory()->create(['email' => 'alya@example.test']);
        $user->role = 'customer';
        $user->save();

        $this->post(route('cart.items.store'), [
            'bouquet_id' => $bouquet->id,
            'quantity' => 2,
        ])->assertRedirect(route('login'));

        $response = $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('cart.index'));
        $this->assertDatabaseHas('cart_items', [
            'user_id' => $user->id,
            'bouquet_id' => $bouquet->id,
            'quantity' => 2,
        ]);
    }

    public function test_customer_cannot_open_admin_pages_or_another_customers_order(): void
    {
        $owner = User::factory()->create();
        $owner->role = 'customer';
        $owner->save();
        $otherCustomer = User::factory()->create();
        $otherCustomer->role = 'customer';
        $otherCustomer->save();
        $bouquet = Bouquet::create([
            'name' => 'Rangkaian Rahasia',
            'slug' => 'rangkaian-rahasia',
            'category' => 'Buket Tangan',
            'price' => 450000,
            'is_active' => true,
        ]);
        $owner->cartItems()->create([
            'bouquet_id' => $bouquet->id,
            'quantity' => 1,
        ]);
        $order = $owner->orders()->create([
            'order_number' => 'BA-261124-OWNER',
            'customer_name' => 'Pemilik Pesanan',
            'phone' => '08123456789',
            'status' => 'pending',
            'subtotal' => 450000,
            'delivery_fee' => 0,
            'total' => 450000,
        ]);

        $this->actingAs($otherCustomer)
            ->get(route('admin.dashboard'))
            ->assertForbidden();

        $this->get(route('customer.orders.show', $order))->assertNotFound();
        $this->get(route('cart.index'))->assertOk()->assertDontSee('Rangkaian Rahasia');
    }

    public function test_checkout_creates_an_order_for_the_signed_in_customer_and_clears_their_cart(): void
    {
        $bouquet = Bouquet::create([
            'name' => 'Rangkaian Rahasia',
            'slug' => 'rangkaian-rahasia',
            'category' => 'Buket Tangan',
            'price' => 450000,
            'is_active' => true,
        ]);
        $customer = User::factory()->create();
        $customer->role = 'customer';
        $customer->save();
        $customer->cartItems()->create([
            'bouquet_id' => $bouquet->id,
            'quantity' => 2,
        ]);

        $response = $this->actingAs($customer)->post(route('checkout.store'), [
            'customer_name' => 'Alya Putri',
            'phone' => '08123456789',
            'delivery_address' => 'Jl. Melati No. 8',
            'occasion' => 'Ulang tahun',
            'message_card' => 'Selamat ulang tahun',
        ]);

        $order = StoreOrder::where('user_id', $customer->id)->firstOrFail();
        $response->assertRedirect(route('customer.orders.show', $order));
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'user_id' => $customer->id,
            'total' => 900000,
        ]);
        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'bouquet_id' => $bouquet->id,
            'quantity' => 2,
        ]);
        $this->assertDatabaseMissing('cart_items', [
            'user_id' => $customer->id,
            'bouquet_id' => $bouquet->id,
        ]);
    }

    public function test_customer_cannot_add_an_inactive_product_to_the_cart(): void
    {
        $bouquet = Bouquet::create([
            'name' => 'Archived Bouquet',
            'slug' => 'archived-bouquet',
            'category' => 'Buket Tangan',
            'price' => 300000,
            'is_active' => false,
        ]);

        $response = $this->post(route('cart.items.store'), [
            'bouquet_id' => $bouquet->id,
            'quantity' => 1,
        ]);

        $response->assertSessionHasErrors('bouquet_id');
        $this->assertDatabaseCount('cart_items', 0);
    }
}
