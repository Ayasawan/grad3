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
        Schema::create('investors', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('user_type');
            $table->string('email')->unique();
            $table->boolean('verified')->default(false);
            $table->string('password');
            $table->string('otp')->nullable();
            $table->string('phone')->nullable();
            $table->text('location')->nullable();
            $table->text('iD_card')->nullable();
            $table->text('personal_photo')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('investors');
    }
};
