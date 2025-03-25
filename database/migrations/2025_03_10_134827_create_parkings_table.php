<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 2025_03_10_134827_create_parkings_table.php
        Schema::create('parkings', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();          // matches 'unique:parkings,name'
            $table->text('description')->nullable();    // matches string validation
            $table->string('address');                  // matches address validation
            $table->integer('total_position');          // matches total_position rules
            $table->boolean('status')->default(true);   // matches boolean requirement
            $table->unsignedBigInteger('region_id');    // matches region_id exists check
            // ... other fields
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parkings');
    }
};
