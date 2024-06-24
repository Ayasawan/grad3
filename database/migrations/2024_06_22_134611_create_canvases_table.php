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
        Schema::create('canvases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->text('target_audience');
            $table->text('customers_using_our_products_or_services');
            $table->text('customer_segments');
            $table->text('most_important_customers');
            $table->text('interacting_with_audience');
            $table->text('strengthening_our_relationship_with_them');
            $table->text('differentiating_our_relationship_from_competitors');
            $table->text('costs_of_building_relationship');
            $table->text('raising_awareness_of_our_existence');
            $table->text('preferred_communication_methods_of_audience');
            $table->text('optimal_communication_methods');
            $table->text('cost_effective_delivery_methods');
            $table->text('value_proposition_for_audience');
            $table->text('problems_of_audience_we_solve');
            $table->text('products_offered_to_each_customer_segment');
            $table->text('audience_needs_we_fulfill');
            $table->text('required_activities_for_our_products');
            $table->text('required_activities_for_communication_channels');
            $table->text('required_activities_for_audience_relationships');
            $table->text('required_activities_for_revenue_generation');
            $table->text('resources_required_for_product_development');
            $table->text('resources_required_for_customer_relationships');
            $table->text('resources_required_for_revenue_generation');
            $table->text('Key_partners');
            $table->text('Key_suppliers');
            $table->text('Key_resources_requested_from_partners');
            $table->text('Key_activities_executed_by_partners');
            $table->text('products_audience_pays_for');
            $table->text('current_payment_methods');
            $table->text('preferred_payment_methods');
            $table->text('revenue_percentage_per_product_for_project');
            $table->text('major_costs_for_project');
            $table->text('most_costly_key_resources');
            $table->text('most_costly_key_activities');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('canvases');
    }
};
