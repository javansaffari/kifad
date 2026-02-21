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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['expense', 'income', 'transfer']);
            $table->integer('amount'); // ریال
            $table->date('date');
            $table->foreignId('main_category_id')->constrained('categories');
            $table->foreignId('sub_category_id')->nullable()->constrained('categories');
            $table->json('tags')->nullable();
            $table->foreignId('from_account_id')->nullable()->constrained('accounts');
            $table->foreignId('to_account_id')->nullable()->constrained('accounts');
            $table->foreignId('person_id')->nullable()->constrained('persons');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
