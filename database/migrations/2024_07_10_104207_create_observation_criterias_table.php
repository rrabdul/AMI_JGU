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
        Schema::create('observation_criterias', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('observation_id')->nullable();
            $table->foreign('observation_id')->references('id')->on('observations')->nullable()->onUpdate('cascade')->onDelete('cascade');            
            $table->unsignedBigInteger('observation_category_id')->nullable();
            $table->foreign('observation_category_id')->references('id')->on('observation_categories')->nullable()->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('observation_criterias');
    }
};