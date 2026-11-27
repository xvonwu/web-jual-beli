<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Kategori: PS5, PS4, XBOX, Others (ID mengikuti flowchart, mis. 161 = Console PS5)
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('platform', 20);          // PS5 | PS4 | XBOX | Others
            $table->string('name');
            $table->string('slug');
            $table->string('seo_slug')->nullable();  // bagian SEO di URL /harga/{platform}/{id}/{page}/{seo}
            $table->unsignedSmallInteger('sort')->default(0);
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->unsignedInteger('price');
            $table->unsignedInteger('old_price')->nullable();   // harga coret (promo)
            $table->unsignedInteger('stock')->default(0);       // 0 = Sold Out
            $table->decimal('weight', 6, 2)->default(0.10);     // kg
            $table->string('region', 20)->nullable();
            $table->date('release_date')->nullable();
            $table->text('description')->nullable();
            $table->string('image')->nullable();                // path di public/ atau URL
            $table->boolean('is_new_release')->default(false);
            $table->boolean('is_preorder')->default(false);
            $table->boolean('is_promo')->default(false);
            $table->timestamps();
        });

        // Keranjang terikat ke user (wajib login)
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('qty')->default(1);
            $table->timestamps();
            $table->unique(['user_id', 'product_id']);
        });

        // Tarif JNE / J&T per kota
        Schema::create('shipping_rates', function (Blueprint $table) {
            $table->id();
            $table->string('courier', 10);   // jne | jnt
            $table->string('city');
            $table->unsignedInteger('cost'); // per kg
            $table->timestamps();
        });

        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->enum('type', ['percent', 'fixed'])->default('fixed');
            $table->unsignedInteger('value');
            $table->unsignedInteger('min_total')->default(0);
            $table->boolean('is_active')->default(true);
            $table->date('expires_at')->nullable();
            $table->timestamps();
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('invoice')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('customer_name');
            $table->string('phone', 20);
            $table->string('email');
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('zip', 10)->nullable();
            $table->string('courier', 10);                         // pickup|gojek|grab|jne|jnt
            $table->unsignedInteger('shipping_cost')->default(0);
            $table->unsignedSmallInteger('payment_method_id');     // 42 = Transfer BCA, 54 = Cash
            $table->unsignedInteger('subtotal');
            $table->unsignedInteger('discount')->default(0);
            $table->string('coupon_code')->nullable();
            $table->unsignedInteger('grand_total');
            $table->text('note')->nullable();
            $table->string('status', 30)->default('menunggu_pembayaran');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('product_name');
            $table->unsignedInteger('price');
            $table->unsignedInteger('qty');
            $table->unsignedInteger('subtotal');
            $table->timestamps();
        });

        Schema::create('payment_confirmations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('email');
            $table->string('nama');
            $table->date('tgl_transfer');
            $table->unsignedInteger('jml_transfer');
            $table->string('rekening_tujuan');
            $table->string('dari_bank');
            $table->string('rekening_pengirim');
            $table->timestamps();
        });

        Schema::create('subscribers', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        foreach (['subscribers', 'payment_confirmations', 'order_items', 'orders', 'coupons',
                  'shipping_rates', 'carts', 'products', 'categories'] as $t) {
            Schema::dropIfExists($t);
        }
    }
};
