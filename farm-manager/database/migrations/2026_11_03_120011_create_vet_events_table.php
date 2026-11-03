<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vet_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farm_id')->constrained();
            $table->foreignId('animal_id')->constrained();
            $table->foreignId('vet_id')->constrained('users');
            $table->string('type'); // vaccination / treatment / exam / test
            $table->foreignId('medication_id')->nullable()->constrained();
            $table->string('dose')->nullable();
            $table->date('event_date');
            $table->string('diagnosis')->nullable();
            $table->text('notes')->nullable();
            $table->date('milk_withdrawal_until')->nullable();
            $table->date('meat_withdrawal_until')->nullable();
            // Общ номер за масов запис – когато една и съща процедура е за група животни.
            $table->uuid('batch_id')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vet_events');
    }
};
