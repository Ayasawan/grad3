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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sender_id');
            $table->string('sender_type');
            $table->unsignedBigInteger('receiver_id');
            $table->string('receiver_type');
            $table->text('content')->nullable();
            $table->tinyInteger('seen')->default(0);
            $table->timestamps();
            // إضافة مفاتيح خارجية
            // $table->foreign('sender')->references('id')->on('users')->onDelete('cascade');
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
