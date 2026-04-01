<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Metal details
            $table->string('metal_type', 50)->nullable()->after('hsn_code'); // gold, silver, platinum, copper
            $table->string('metal_purity', 20)->nullable()->after('metal_type'); // 14K, 18K, 22K, 24K, 925, 950
            $table->string('metal_color', 30)->nullable()->after('metal_purity'); // yellow, white, rose
            $table->decimal('metal_weight', 8, 3)->nullable()->after('metal_color'); // grams
            $table->decimal('making_charge_percent', 5, 2)->nullable()->after('metal_weight');

            // Stone details
            $table->string('stone_type', 50)->nullable()->after('making_charge_percent'); // diamond, ruby, emerald, etc.
            $table->decimal('stone_weight', 8, 3)->nullable()->after('stone_type'); // carats
            $table->string('stone_clarity', 20)->nullable()->after('stone_weight'); // IF, VVS1, VVS2, VS1, VS2, SI1, SI2
            $table->string('stone_cut', 30)->nullable()->after('stone_clarity'); // excellent, very good, good, fair
            $table->string('stone_color', 20)->nullable()->after('stone_cut'); // D, E, F, G, H, I, J (for diamonds)

            // Certification
            $table->string('hallmark_number', 50)->nullable()->after('stone_color');
            $table->string('certification_type', 30)->nullable()->after('hallmark_number'); // BIS, IGI, GIA
            $table->string('certification_number', 50)->nullable()->after('certification_type');

            // Jewellery-specific
            $table->string('occasion', 100)->nullable()->after('certification_number'); // wedding, engagement, casual, party
            $table->boolean('is_customizable')->default(false)->after('occasion');
            $table->boolean('engraving_available')->default(false)->after('is_customizable');
            $table->decimal('engraving_cost', 10, 2)->nullable()->after('engraving_available');
            $table->text('care_instructions')->nullable()->after('engraving_cost');

            // Indexes for filtering
            $table->index('metal_type');
            $table->index('metal_purity');
            $table->index('stone_type');
            $table->index('certification_type');
        });

        // Add customization fields to cart_items
        Schema::table('cart_items', function (Blueprint $table) {
            $table->string('engraving_text', 100)->nullable()->after('attributes');
            $table->string('ring_size', 20)->nullable()->after('engraving_text');
            $table->text('custom_note')->nullable()->after('ring_size');
            $table->boolean('gift_wrap')->default(false)->after('custom_note');
            $table->string('gift_message', 500)->nullable()->after('gift_wrap');
        });

        // Add customization snapshot to order_items
        Schema::table('order_items', function (Blueprint $table) {
            $table->json('customization')->nullable()->after('product_snapshot');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['metal_type']);
            $table->dropIndex(['metal_purity']);
            $table->dropIndex(['stone_type']);
            $table->dropIndex(['certification_type']);

            $table->dropColumn([
                'metal_type', 'metal_purity', 'metal_color', 'metal_weight', 'making_charge_percent',
                'stone_type', 'stone_weight', 'stone_clarity', 'stone_cut', 'stone_color',
                'hallmark_number', 'certification_type', 'certification_number',
                'occasion', 'is_customizable', 'engraving_available', 'engraving_cost', 'care_instructions',
            ]);
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropColumn(['engraving_text', 'ring_size', 'custom_note', 'gift_wrap', 'gift_message']);
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('customization');
        });
    }
};
