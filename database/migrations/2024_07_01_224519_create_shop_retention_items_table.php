<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('shop_retention_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_id')->constrained()->on('shops')->cascadeOnDelete();
            $table->smallInteger('code');
            $table->string('tax_code');
            $table->decimal('base');
            $table->decimal('porcentage');
            $table->decimal('value');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shop_retention_items');
    }
};
