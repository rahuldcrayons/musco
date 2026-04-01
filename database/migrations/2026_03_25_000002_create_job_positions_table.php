<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_positions', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('department', 100)->nullable();
            $table->string('location', 100)->default('Remote');
            $table->string('type', 50)->default('Full-time'); // Full-time, Part-time, Contract, Internship
            $table->text('description')->nullable();
            $table->json('requirements')->nullable();
            $table->unsignedSmallInteger('position')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['is_active', 'position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_positions');
    }
};
