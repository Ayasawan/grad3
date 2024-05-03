<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Http\Controllers\Controller;
use App\Http\Resources\ComplaintResource;
use App\Traits\ApiResponseTrait;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;


use Illuminate\Http\Request;

class ComplaintController extends Controller
{
    use  ApiResponseTrait;

    public function index()
    {
        $Complaint = ComplaintResource::collection(Complaint::get());
        return $this->apiResponse($Complaint, 'ok', 200);
    }


    public function store(Request $request)
    {
        $input=$request->all();
        $validator = Validator::make( $input, [
            'description' => 'required',
            'project_id' => 'required',
           
        ]);
        if ($validator->fails()) {
            return $this->apiResponse(null, $validator->errors(), 400);
        }

        $Complaint = Complaint::query()->create([
            'description' => $request->description,
            'project_id' => $request->project_id,
            'investor_id' => Auth::id(),  
        ]);


        if ($Complaint) {
            return $this->apiResponse(new ComplaintResource($Complaint), 'the Complaint  save', 201);
        }
        return $this->apiResponse(null, 'the Complaint  not save', 400);
    }

   
    public function show( $id)
    {
        $Complaint= Complaint::find($id);
        if($Complaint){
            return $this->apiResponse(new ComplaintResource($Complaint) , 'ok' ,200);
        }
        return $this->apiResponse(null ,'the Complaint not found' ,404);
    }

   
    public function update(Request $request, $id)
    {
        $Complaint= Complaint::find($id);
        if(!$Complaint)
        {
            return $this->apiResponse(null ,'the Complaint not found ',404);
        }

        $Complaint->update($request->all());
        if($Complaint)
        {
            return $this->apiResponse(new ComplaintResource($Complaint) , 'the Complaint update',201);

        }
    }

   
    public function destroyAdmin( $id)
    {
        $complaint =  Complaint::find($id);

        if(!$complaint){
            return $this->apiResponse(null, 'This Complaint not found', 404);
        }

        $complaint->delete($id);
            return $this->apiResponse(null, 'This complaint deleted', 200);
    }


    public function destroyInvestor($id)
{
    $complaint = Complaint::find($id);

    if (!$complaint) {
        return $this->apiResponse(null, 'This Complaint not found', 404);
    }

    // قم بالتحقق مما إذا كان المستثمر المتصل هو نفسه الذي أضاف الشكوى
    if ($complaint->investor_id !== Auth::user()->id) {
        return $this->apiResponse(null, 'You are not authorized to delete this complaint', 403);
    }

    $complaint->delete();
    return $this->apiResponse(null, 'This complaint deleted', 200);
}
}
