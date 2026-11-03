<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('animals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farm_id')->constrained();
            $table->string('ear_tag')->unique();
            $table->foreignId('species_id')->constrained();
            $table->foreignId('breed_id')->nullable()->constrained();
            $table->string('sex'); // female / male
            $table->date('birth_date')->nullable();
            $table->foreignId('mother_id')->nullable()->constrained('animals')->nullOnDelete();
            $table->string('group_name')->nullable();
            $table->string('status')->default('active');
            $table->timestamp('status_changed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('animals');
    }
};
