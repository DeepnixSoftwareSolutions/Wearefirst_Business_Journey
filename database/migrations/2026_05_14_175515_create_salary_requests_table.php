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
        Schema::create('salary_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // The Agent
            $table->decimal('amount', 15, 2);
            $table->string('status')->default('Pending Admin Approval');
            $table->foreignId('requested_by')->constrained('users')->onDelete('cascade'); // The Accountant
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null'); // The Admin/Manager
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salary_requests');
    }
};
