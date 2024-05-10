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
        Schema::create('investor_interest', function (Blueprint $table) {
            $table->unsignedBigInteger('investor_id');
            $table->unsignedBigInteger('interest_id');
            $table->timestamps();

            $table->foreign('investor_id')->references('id')->on('investors')->onDelete('cascade');
            $table->foreign('interest_id')->references('id')->on('interests')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('investor_interest');
    }
};
