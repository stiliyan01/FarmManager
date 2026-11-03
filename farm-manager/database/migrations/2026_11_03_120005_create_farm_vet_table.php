<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // История на назначенията: старите редове не се трият, а се затварят с valid_to.
        Schema::create('farm_vet', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farm_id')->constrained();
            $table->foreignId('vet_id')->constrained('users');
            $table->foreignId('assigned_by')->constrained('users');
            $table->date('valid_from');
            $table->date('valid_to')->nullable(); // null = назначението е активно
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('farm_vet');
    }
};
