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
            $table->string('report_title')->nullable(false);
            $table->text('achieved_goals_summary')->nullable(false);
            $table->text('unachieved_goals_summary')->nullable(false);
            $table->decimal('investor_amount', 10, 2)->nullable(false);
            $table->decimal('total_revenue', 10, 2)->nullable(false);
            $table->decimal('total_costs', 10, 2)->nullable(false);
            $table->decimal('net_profit', 10, 2)->nullable(false);
            $table->decimal('net_profit_employer', 10, 2)->nullable(false);
            $table->decimal('net_profit_investor', 10, 2)->nullable(false);
            $table->text('received_materials')->nullable(false);
            $table->decimal('material_price', 10, 2)->nullable(false);
            $table->decimal('total_sales', 10, 2)->nullable(false);
            $table->decimal('overall_net_profit', 10, 2)->nullable(false);
            $table->decimal('maintenance_amount', 10, 2)->nullable();
            $table->decimal('wages_and_transactions_amount', 10, 2)->nullable();
            $table->text('main_recommendations')->nullable();
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
