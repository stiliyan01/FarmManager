<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('farms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('region_id')->constrained();
            $table->string('reg_number')->unique();
            $table->string('name');
            $table->string('settlement');
            // Празно, докато собственикът не приеме поканата.
            $table->foreignId('owner_id')->nullable()->constrained('users');
            $table->unsignedInteger('capacity')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('farms');
    }
};
