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
        Schema::table('orders', function (Blueprint $table) {
           $table->string('recipient_name', 255)->after('user_id');
           $table->string('phone', 32)->after('recipient_name');
           $table->string('address', 255)->after('phone');
           $table->text('comment')->nullable()->after('address');
           $table->decimal('total', 10, 2)->after('comment');
           $table->string('currency', 3)->default('UAH')->after('total');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'recipient_name',
                'phone',
                'address',
                'comment',
                'total',
                'currency',
            ]);
        });
    }
};
