<?php

namespace App\Http\Controllers;
use App\Traits\ApiResponseTrait;

use App\Models\Investor;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\InvestorResource;


class InvestorController extends Controller
{
    use ApiResponseTrait;

    public function index()
    {
        $Investor = InvestorResource::collection(Investor::get());
        return $this->apiResponse($Investor, 'ok', 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $Investor= Investor::find($id);
        if($Investor){
            return $this->apiResponse(new InvestorResource($Investor) , 'ok' ,200);
        }
        return $this->apiResponse(null ,'the Investor not found' ,404);
   
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,  $id)
    {
        $Investor= Investor::find($id);
        if(!$Investor)
        {
            return $this->apiResponse(null ,'the Investor not found ',404);
        }

        $Investor->update($request->all());
        if($Investor)
        {
            return $this->apiResponse(new InvestorResource($Investor) , 'the Investor update',201);

        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy( $id)
    {
        $Investor =  Investor::find($id);

        if(!$Investor){
            return $this->apiResponse(null, 'This Investor not found', 404);
        }

        $Investor->delete($id);
            return $this->apiResponse(null, 'This Investor deleted', 200);
    }



    
}
