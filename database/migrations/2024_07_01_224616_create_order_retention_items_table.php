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
        Schema::create('order_retention_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->on('orders')->cascadeOnDelete();
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
        Schema::dropIfExists('order_retention_items');
    }
};
