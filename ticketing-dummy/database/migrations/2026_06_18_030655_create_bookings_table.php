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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('order_id')->unique();
            $table->string('snap_token')->nullable();
            $table->string('midtrans_trans_id')->nullable();
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone');
            $table->unsignedInteger('ticket_qty');
            $table->unsignedInteger('total_price');
            $table->enum('status', ['PENDING', 'SUCCESS', 'FAILED', 'EXPIRED'])->default('PENDING');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
