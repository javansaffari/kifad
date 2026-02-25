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
        Schema::create('cheques', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['issued', 'received']);
            $table->bigInteger('amount');
            $table->string('serial_number');
            $table->string('sayad_id');
            $table->foreignId('person_id')->constrained('persons');
            $table->foreignId('account_id')->constrained('accounts');
            $table->date('issue_date');
            $table->date('due_date');
            $table->string('bank');
            $table->json('tags')->nullable();
            $table->text('description')->nullable();
            $table->boolean('reminder')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cheques');
    }
};
