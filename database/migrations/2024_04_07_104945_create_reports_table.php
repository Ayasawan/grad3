<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->string('عنوان_التقرير')->nullable(false);
            $table->text('ملخص_الأهداف_المحققة')->nullable(false);
            $table->text('ملخص_الأهداف_غير_المحققة')->nullable(false);
            $table->decimal('مبلغ_المستثمر', 10, 2)->nullable(false);
            $table->decimal('الإيرادات_الإجمالية', 10, 2)->nullable(false);
            $table->decimal('التكاليف_الإجمالية', 10, 2)->nullable(false);
            $table->decimal('الأرباح_الصافية', 10, 2)->nullable(false);
            $table->decimal('الصافي_الربح_لصاحب_العمل', 10, 2)->nullable(false);
            $table->decimal('الصافي_الربح_للمستثمر', 10, 2)->nullable(false);
            $table->text('المواد_المستلمة')->nullable(false);
            $table->decimal('سعر_المواد', 10, 2)->nullable(false);
            $table->decimal('إجمالي_المبيعات', 10, 2)->nullable(false);
            $table->decimal('صافي_الربح_الكلي', 10, 2)->nullable(false);
            $table->decimal('مبلغ_الصيانة', 10, 2)->nullable();
            $table->decimal('مبلغ_الأجور_والمعاملات', 10, 2)->nullable();
            $table->text('التوصيات_الرئيسية')->nullable();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

  
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};