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
        Schema::create('loan_installments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('loan_id');
            $table->bigInteger('installment_number');
            $table->bigInteger('amount');
            $table->date('due_date');
            $table->boolean('paid')->default(false);
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->foreign('loan_id')->references('id')->on('loans')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_installments');
    }
};
