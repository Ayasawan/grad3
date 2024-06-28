<?php

namespace App\Http\Controllers;

use App\Models\Canvas;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Traits\ApiResponseTrait;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\CanvasResource;


class CanvasController extends Controller
{
    use  ApiResponseTrait;

    public function index()
    {
        $Canvas = CanvasResource::collection(Canvas::get());
        return $this->apiResponse($Canvas, 'ok', 200);
    }



    public function store(Request $request , $project_id)
    {

        $input=$request->all();

        $validator = Validator::make( $input, [
            
            'target_audience' => 'required',
            'customers_using_our_products_or_services' => 'required',
            'customer_segments' => 'required',
            'most_important_customers' => 'required',
            'interacting_with_audience' => 'required',
            'strengthening_our_relationship_with_them' => 'required',
            'differentiating_our_relationship_from_competitors' => 'required',
            'costs_of_building_relationship' => 'required',
            'raising_awareness_of_our_existence' => 'required',
            'preferred_communication_methods_of_audience' => 'required',
            'optimal_communication_methods' => 'required',
            'cost_effective_delivery_methods' => 'required',
            'value_proposition_for_audience' => 'required',
            'problems_of_audience_we_solve' => 'required',
            'products_offered_to_each_customer_segment' => 'required',
            'audience_needs_we_fulfill' => 'required',
            'required_activities_for_our_products' => 'required',
            'required_activities_for_communication_channels' => 'required',
            'required_activities_for_audience_relationships' => 'required',
            'required_activities_for_revenue_generation' => 'required',
            'resources_required_for_product_development' => 'required',
            'resources_required_for_customer_relationships' => 'required',
            'resources_required_for_revenue_generation' => 'required',
            'key_partners' => 'required',
            'key_suppliers' => 'required',
            'key_resources_requested_from_partners' => 'required',
            'key_activities_executed_by_partners' => 'required',
            'products_audience_pays_for' => 'required',
            'current_payment_methods' => 'required',
            'preferred_payment_methods' => 'required',
            'revenue_percentage_per_product_for_project' => 'required',
            'major_costs_for_project' => 'required',
            'most_costly_key_resources' => 'required',
            'most_costly_key_activities' => 'required',
          
         
          
            
        ]);
        if ($validator->fails()) {
            return $this->apiResponse(null, $validator->errors(), 400);
        }


        $Canvas = Canvas::query()->create([
            'project_id' => $project_id,
            'target_audience' => $request->target_audience,
            'customers_using_our_products_or_services' => $request->customers_using_our_products_or_services,
            'customer_segments' => $request->customer_segments,
            'most_important_customers' => $request->most_important_customers,
            'interacting_with_audience' => $request->interacting_with_audience,
            'strengthening_our_relationship_with_them' => $request->strengthening_our_relationship_with_them,
            'differentiating_our_relationship_from_competitors' => $request->differentiating_our_relationship_from_competitors,
            'costs_of_building_relationship' => $request->costs_of_building_relationship,
            'raising_awareness_of_our_existence' => $request->raising_awareness_of_our_existence,
            'preferred_communication_methods_of_audience' => $request->preferred_communication_methods_of_audience,
            'optimal_communication_methods' => $request->optimal_communication_methods,
            'cost_effective_delivery_methods' => $request->cost_effective_delivery_methods,
            'value_proposition_for_audience' => $request->value_proposition_for_audience,
            'problems_of_audience_we_solve' => $request->problems_of_audience_we_solve,
            'products_offered_to_each_customer_segment' => $request->products_offered_to_each_customer_segment,
            'audience_needs_we_fulfill' => $request->audience_needs_we_fulfill,
            'required_activities_for_our_products' => $request->required_activities_for_our_products,
            'required_activities_for_communication_channels' => $request->required_activities_for_communication_channels,
            'required_activities_for_audience_relationships' => $request->required_activities_for_audience_relationships,
            'required_activities_for_revenue_generation' => $request->required_activities_for_revenue_generation,
            'resources_required_for_product_development' => $request->resources_required_for_product_development,
            'resources_required_for_customer_relationships' => $request->resources_required_for_customer_relationships,
            'resources_required_for_revenue_generation' => $request->resources_required_for_revenue_generation,
            'key_partners' => $request->key_partners,
            'key_suppliers' => $request->key_suppliers,
            'key_resources_requested_from_partners' => $request->key_resources_requested_from_partners,
            'key_activities_executed_by_partners' => $request->key_activities_executed_by_partners,
            'products_audience_pays_for' => $request->products_audience_pays_for,
            'current_payment_methods' => $request->current_payment_methods,
            'preferred_payment_methods' => $request->preferred_payment_methods,
            'revenue_percentage_per_product_for_project' => $request->revenue_percentage_per_product_for_project,
            'major_costs_for_project' => $request->major_costs_for_project,
            'most_costly_key_resources' => $request->most_costly_key_resources,
            'most_costly_key_activities' => $request->most_costly_key_activities,
            
          
        ]);

        if ($Canvas) {
            return $this->apiResponse(new CanvasResource($Canvas), 'the Canvas  save', 201);
        }
        return $this->apiResponse(null, 'the Canvas  not save', 400);
    }


   
    public function show($project_id)
    {
        $canvasList = Canvas::where('project_id', $project_id)->get();
    
        if ($canvasList->isNotEmpty()) {
            return $this->apiResponse(CanvasResource::collection($canvasList), 'ok', 200);
        }
    
        return $this->apiResponse(null, 'No Canvas found for the project', 404);
    }

    

    public function update(Request $request, $project_id)
    {
    $canvas = Canvas::where('project_id', $project_id)->first();

    if (!$canvas) {
        return $this->apiResponse(null, 'The Canvas was not found', 404);
    }

    $canvas->update($request->all());

    return $this->apiResponse(new CanvasResource($canvas), 'The Canvas has been updated', 200);
    }


    
    public function destroy($project_id)
    {
        $canvas = Canvas::where('project_id', $project_id)->first();
    
        if (!$canvas) {
            return $this->apiResponse(null, 'The Canvas was not found', 404);
        }
    
        $canvas->delete();
    
        return $this->apiResponse(null, 'The Canvas has been deleted', 200);
    }
    
}
