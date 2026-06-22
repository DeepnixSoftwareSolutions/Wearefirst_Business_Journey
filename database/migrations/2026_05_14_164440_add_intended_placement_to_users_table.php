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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('intended_parent_node_id')->nullable()->constrained('nodes')->onDelete('set null');
            $table->enum('intended_position', ['Left', 'Right'])->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['intended_parent_node_id']);
            $table->dropColumn(['intended_parent_node_id', 'intended_position']);
        });
    }
};
