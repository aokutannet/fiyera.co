<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('image_path')->nullable()->after('description');
            $table->string('category')->nullable()->after('image_path');
            
            // Stock Tracking
            $table->boolean('stock_tracking')->default(false)->after('category');
            $table->integer('stock')->default(0)->after('stock_tracking');
            $table->boolean('critical_stock_alert')->default(false)->after('stock');
            $table->integer('critical_stock_quantity')->nullable()->after('critical_stock_alert');

            // Advanced Pricing
            $table->decimal('buying_price', 10, 4)->nullable()->after('critical_stock_quantity');
            $table->string('buying_currency')->default('TRY')->after('buying_price');
            
            // Renaming/Adjusting existing price to be "selling_price" concept or just adding new ones?
            // User asked for "Vergiler Hariç Satış Fiyatı" (Tax Excl Selling Price).
            // We already have 'price' and 'vat_rate'. Let's treat existing 'price' as 'selling_price' (Tax Excl).
            // We'll add selling_currency.
            $table->string('selling_currency')->default('TRY')->after('price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'image_path',
                'category',
                'stock_tracking',
                'stock',
                'critical_stock_alert',
                'critical_stock_quantity',
                'buying_price',
                'buying_currency',
                'selling_currency',
            ]);
        });
    }
};
