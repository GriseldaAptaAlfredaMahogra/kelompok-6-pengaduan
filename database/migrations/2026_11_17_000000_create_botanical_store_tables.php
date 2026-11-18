<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('contact_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->timestamps();
        });

        Schema::create('flowers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('variety')->nullable();
            $table->string('category')->default('Bunga Utama');
            $table->string('unit')->default('tangkai');
            $table->unsignedInteger('low_stock_threshold')->default(10);
            $table->text('image_url')->nullable();
            $table->timestamps();
        });

        Schema::create('flower_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('flower_id')->constrained()->cascadeOnDelete();
            $table->foreignId('supplier_id')->nullable()->constrained()->nullOnDelete();
            $table->string('batch_code')->unique();
            $table->unsignedInteger('quantity');
            $table->decimal('purchase_price', 12, 2)->default(0);
            $table->string('cold_room_location')->nullable();
            $table->date('received_at')->nullable();
            $table->date('expires_at')->nullable();
            $table->timestamps();
        });

        Schema::create('bouquets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('category')->default('Buket Tangan');
            $table->text('description')->nullable();
            $table->decimal('price', 12, 2);
            $table->text('image_url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->string('customer_name');
            $table->string('phone');
            $table->text('delivery_address')->nullable();
            $table->string('occasion')->nullable();
            $table->dateTime('requested_delivery_at')->nullable();
            $table->string('status')->default('pending')->index();
            $table->text('customization_notes')->nullable();
            $table->text('message_card')->nullable();
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('delivery_fee', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('stock_released_at')->nullable();
            $table->timestamps();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('bouquet_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('flower_id')->nullable()->constrained()->nullOnDelete();
            $table->string('item_name');
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('unit_price', 12, 2);
            $table->decimal('line_total', 12, 2);
            $table->timestamps();
        });

        Schema::create('order_item_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('flower_batch_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('quantity');
            $table->timestamps();
            $table->unique(['order_item_id', 'flower_batch_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_item_batches');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('bouquets');
        Schema::dropIfExists('flower_batches');
        Schema::dropIfExists('flowers');
        Schema::dropIfExists('suppliers');
    }
};
