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

            // Level 0 = Has never paired. 
            // Level 1 = Has hit 1:1 (needs 2:2 next).
            // Level 2 = Has hit 2:2 (needs 3:3 next).
            $table->integer('current_pair_level')->default(0);
            
            // Income Tracking (Node Specific)
            $table->decimal('today_income_projected', 15, 2)->default(0.00);
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