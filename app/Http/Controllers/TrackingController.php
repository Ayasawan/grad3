<?php

namespace App\Http\Controllers;
use App\Traits\ApiResponseTrait;

use App\Models\Tracking;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\TrackingResource;


class TrackingController extends Controller
{
    use ApiResponseTrait;

    public function index()
    {
        $Tracking = TrackingResource::collection(Tracking::get());
        return $this->apiResponse($Tracking, 'ok', 200);
    }

    
    public function store(Request $request)
    {
        $input=$request->all();
        $validator = Validator::make( $input, [
            'earning' => 'required',
            'cost' => 'required',
            'tax' => 'required',
            'outcome' => 'required',
            'resources_change' => 'required',
            'project_id' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->apiResponse(null, $validator->errors(), 400);
        }

        $Tracking =Tracking::create($request->all());

        if ($Tracking) {
            return $this->apiResponse(new TrackingResource($Tracking), 'the Tracking  save', 201);
        }
        return $this->apiResponse(null, 'the Tracking  not save', 400);
    }

 
    public function show( $id)
    {
        $Tracking= Tracking::find($id);
        if($Tracking){
            return $this->apiResponse(new TrackingResource($Tracking) , 'ok' ,200);
        }
        return $this->apiResponse(null ,'the Tracking not found' ,404);
    }

    
    public function update(Request $request,  $id)
    {
        $Tracking= Tracking::find($id);
        if(!$Tracking)
        {
            return $this->apiResponse(null ,'the Tracking not found ',404);
        }

        $Tracking->update($request->all());
        if($Tracking)
        {
            return $this->apiResponse(new TrackingResource($Tracking) , 'the Tracking update',201);

        }
    }

    
    public function destroy( $id)
    {
        $Tracking =  Tracking::find($id);

        if(!$Tracking){
            return $this->apiResponse(null, 'This Tracking not found', 404);
        }

        $Tracking->delete($id);
            return $this->apiResponse(null, 'This Tracking deleted', 200);
    }
    
}
