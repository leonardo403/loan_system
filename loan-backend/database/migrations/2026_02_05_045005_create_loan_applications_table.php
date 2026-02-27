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
        Schema::create('loan_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->decimal('requested_amount', 12, 2);
            $table->text('purpose')->nullable();
            $table->string('status', 50)->default('pending');
            $table->integer('credit_score')->nullable();
            $table->string('risk_category', 20)->nullable();
            $table->string('approval_status', 50)->nullable();
            $table->decimal('approved_amount', 12, 2)->nullable();
            $table->decimal('interest_rate', 5, 2)->nullable();
            $table->integer('max_term')->nullable();
            $table->text('justification')->nullable();
            $table->json('recommendations')->nullable();
            $table->json('conditions')->nullable();
            $table->json('ai_analysis')->nullable();
            $table->timestamps();
            
            $table->index(['client_id', 'created_at']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_applications');
    }
};
