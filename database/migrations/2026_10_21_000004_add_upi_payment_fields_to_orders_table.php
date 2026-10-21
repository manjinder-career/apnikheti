<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('transaction_id')->nullable()->after('payment_status')->index();
            $table->timestamp('payment_verified_at')->nullable()->after('transaction_id');
            $table->string('upi_id')->nullable()->after('payment_verified_at');
            $table->text('payment_notes')->nullable()->after('upi_id');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['transaction_id', 'payment_verified_at', 'upi_id', 'payment_notes']);
        });
    }
};
