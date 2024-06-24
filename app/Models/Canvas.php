<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Canvas extends Model
{
    use HasFactory;

    protected $table = "canvases";

    protected $fillable = ['project_id','target_audience','customers_using_our_products_or_services','customer_segments',
    'most_important_customers','interacting_with_audience','strengthening_our_relationship_with_them',
    'differentiating_our_relationship_from_competitors','costs_of_building_relationship','raising_awareness_of_our_existence',
    'preferred_communication_methods_of_audience','optimal_communication_methods','cost_effective_delivery_methods',
    'value_proposition_for_audience','problems_of_audience_we_solve','products_offered_to_each_customer_segment',
    'audience_needs_we_fulfill','required_activities_for_our_products','required_activities_for_communication_channels',
    'required_activities_for_audience_relationships','required_activities_for_revenue_generation','resources_required_for_product_development',
    'resources_required_for_customer_relationships','resources_required_for_revenue_generation','key_partners',
    'key_suppliers','key_resources_requested_from_partners','key_activities_executed_by_partners',
    'products_audience_pays_for','current_payment_methods','preferred_payment_methods',
    'revenue_percentage_per_product_for_project','major_costs_for_project','most_costly_key_resources',
    'most_costly_key_activities', ];

    protected $primaryKey = "id";
    public $timestamps = true ;



    public function project(){
        return $this->belongsTo(User::class,'project_id');
    }

}
