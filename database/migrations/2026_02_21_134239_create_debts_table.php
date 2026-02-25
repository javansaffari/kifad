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
        Schema::create('debts', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['borrow', 'lend']);
            $table->bigInteger('amount');
            $table->date('due_date');
            $table->foreignId('account_id')->constrained('accounts');
            $table->foreignId('person_id')->constrained('persons');
            $table->json('tags')->nullable();
            $table->text('description')->nullable();
            $table->boolean('reminder')->default(false);
            $table->bigInteger('paid_amount')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('debts');
    }
};
