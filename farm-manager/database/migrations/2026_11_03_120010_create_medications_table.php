<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medications', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('active_substance')->nullable();
            // Карантинен срок в дни – колко дни след третирането не се ползва мляко / месо.
            $table->unsignedSmallInteger('withdrawal_milk_days')->default(0);
            $table->unsignedSmallInteger('withdrawal_meat_days')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medications');
    }
};
