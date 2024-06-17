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

    //for_admin
    public function index()
    {
        $Complaint = ComplaintResource::collection(Complaint::get());
        return $this->apiResponse($Complaint, 'ok', 200);
    }



     //for_investor
    public function store(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'description' => 'required',
            'project_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(null, $validator->errors(), 400);
        }

        $investor = Auth::user(); 

        $complaint = Complaint::create([
            'description' => $request->description,
            'project_id' => $request->project_id,
            'investor_id' => $investor->id,  
        ]);

        if ($complaint) {
            //notification
            $title = 'تقديم شكوى';
            $body = "عزيزي/عزيزتي {$investor->first_name}، تم تقديم شكواك بنجاح وسيتم معالجتها في أقرب وقت ممكن. سيتم إشعارك بأي تحديثات إضافية.";
            $this->sendNotificationAndStore($investor->id, 'investor', $title, $body);

            return $this->apiResponse(new ComplaintResource($complaint), 'تم حفظ الشكوى بنجاح', 201);
        }

        return $this->apiResponse(null, 'لم يتم حفظ الشكوى', 400);
    }


   

    //for_admin
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

   //for_admin
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
