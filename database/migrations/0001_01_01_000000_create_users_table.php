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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            
            // Basic Info
            $table->unsignedInteger('member_id')->unique()->nullable();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('nic')->unique();
            $table->enum('nic_type', ['Own', 'Mother', 'Father', 'Guardian'])->default('Own');
            $table->string('phone');

            // Location Data
            $table->string('district')->nullable();
            $table->string('city')->nullable();
            $table->text('address')->nullable();

            $table->string('profile_image_path')->nullable();
            
            // System Roles & Status
            $table->enum('role', ['Admin', 'Manager', 'Accountant', 'Agent', 'Student'])->default('Student');
            $table->boolean('middle_legs_unlocked')->default(false);
            $table->enum('badge', ['None', 'Ruby', 'Diamond', 'Crown Diamond', '2 Crown Diamond'])->default('None');
            $table->boolean('is_held')->default(false);
            $table->json('special_offers')->nullable();
            $table->json('beneficiary_details')->nullable();
            $table->json('bank_details')->nullable();
            
            // Financials
            $table->decimal('pending_payout_balance', 15, 2)->default(0.00); 
            $table->decimal('total_historical_earned', 15, 2)->default(0.00);
            
            // Admission & Registration Tracking (Unified)
            $table->decimal('admission_fee_paid', 15, 2)->default(0.00);
            $table->string('admission_status')->default('Pending Payment');
            $table->string('payment_transaction_id')->nullable(); 
            
            // Note: registered_by_agent_id references the 'users' table itself, so this IS safe to keep here!
            $table->foreignId('registered_by_agent_id')->nullable()->constrained('users')->onDelete('set null');

            // Auth
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};