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
        Schema::create('nodes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('parent_node_id')->nullable()->constrained('nodes')->onDelete('cascade');
            $table->enum('position', ['Left', 'Right'])->nullable(); // Root node will have null
            $table->enum('account_type', ['Main', 'Sub_A', 'Sub_B']);
            
            // Point Tracking
            $table->integer('left_points')->default(0);
            $table->integer('right_points')->default(0);

            // Level 0 = Has never paired (Waiting for 1:1)
            // Level 1 = Has hit 1:1 (Waiting for 2:2)
            // Level 2 = Has hit 2:2 (Waiting for 3:3)
            // Level 3 = Has hit 3:3 (Matured, loops at 3:3)
            $table->integer('current_pair_level')->default(0);
            
            // Daily Throttle Tracker
            $table->date('last_payout_date')->nullable();
            
            $table->decimal('node_cumulative_pending', 15, 2)->default(0.00);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nodes');
    }
};