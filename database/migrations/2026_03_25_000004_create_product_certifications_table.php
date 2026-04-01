<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_certifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('type', 30); // BIS, IGI, GIA, SGL, AGS
            $table->string('number', 100);
            $table->string('issuer', 100)->nullable();
            $table->date('issued_date')->nullable();
            $table->string('image_url')->nullable();
            $table->string('document_url')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->timestamps();

            $table->index(['product_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_certifications');
    }
};
