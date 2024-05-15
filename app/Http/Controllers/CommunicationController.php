<?php

namespace App\Http\Controllers;

use App\Models\Communication;
use App\Models\Investor;
use App\Models\Project;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Traits\ApiResponseTrait;
use Illuminate\Support\Facades\Validator;
use Illuminate\Notifications\Notifiable;
use App\Http\Resources\ReportResource;
use App\Http\Resources\CommunicationResource;



class CommunicationController extends Controller
{

    use  ApiResponseTrait;
    /**
     * Display a listing of the resource.
     */
   
     public function index()
     {
         $Communication = CommunicationResource::collection(Communication::get());
         return $this->apiResponse($Communication, 'ok', 200);
     }

    /**
     * Store a newly created resource in storage.
        */
        // public function store($id)
        // {
        //     $project = Project::find($id);
        
        //     if (!$project) {
        //         return $this->apiResponse(null, 'مشروع غير موجود', 404);
        //     }
        
        //     if ($project->accept_status != 1) {
        //         return $this->apiResponse(null, 'غير مسموح بإنشاء طلب التواصل لهذا المشروع', 400);
        //     }
        
        //     $communication = Communication::create([
        //         'project_id' => $id,
        //         'investor_id' => Auth::id(),
        //         'status' => false,
        //     ]);
        
        //     if ($communication) {
        //         return $this->apiResponse(new CommunicationResource($communication), 'تم حفظ طلب التواصل', 201);
        //     }
        
        //     return $this->apiResponse(null, 'فشل في حفظ طلب التواصل', 400);
        // }





public function store(Request $request, $id)
{
    $project = Project::find($id);

    if (!$project) {
        return $this->apiResponse(null, 'مشروع غير موجود', 404);
    }

    if ($project->accept_status != 1) {
        return $this->apiResponse(null, 'غير مسموح بإنشاء طلب التواصل لهذا المشروع', 400);
    }

    $communication = Communication::create([
        'project_id' => $id,
        'investor_id' => Auth::id(),
        'status' => false,
    ]);




    if ($communication) {
        // Save additional information for the investor (ID card and personal photo)
        $investor = Investor::find(Auth::id());
        $investor->iD_card = $request->input('iD_card');
        $investor->personal_photo = $request->input('personal_photo');

        $validator = Validator::make($request->all(), [
            'iD_card' => ['nullable'],
            'personal_photo' => ['nullable'],
        ]);
        

        $IDCardFile=$this->saveImage($request->iD_card,'images/user');
        $personalPhotoFile=$this->saveImage($request->personal_photo,'images/user');

        if ($validator->fails()) {
            return $this->apiResponse(null, $validator->errors(), 400);
        }

       
        $investorData = [
            'iD_card' => $IDCardFile,
            'personal_photo' => $personalPhotoFile,
        
        ];
        $investor->update($investorData);

        return $this->apiResponse(new CommunicationResource($communication), 'تم حفظ طلب التواصل والمعلومات الإضافية', 201);
    }

    return $this->apiResponse(null, 'فشل في حفظ طلب التواصل', 400);
}
        

    public function show($id)
    {
        $communication = Communication::with('project', 'investor')->find($id);
    
        if (!$communication) {
            return response()->json(['message' => 'لم يتم العثور على طلب التواصل.'], 404);
        }
    
        return response()->json(['communication' => $communication]);
    }


    public function acceptRequest($id)
    {
        $communication = Communication::find($id);
    
        if (!$communication) {
            return response()->json(['message' => 'Communication request not found.'], 404);
        }
    
        $communication->status = 1;
        $communication->save();
    
        // Update investment status in the associated project table
        $project = Project::find($communication->project_id);
        if ($project) {
            $project->investment_status = 1;
            $project->investor_id = $communication->investor_id; // Store investor number
            $project->save();
        }
    
        return response()->json(['message' => 'Communication request accepted successfully.']);
    }
    
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Communication $communication)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Communication $communication)
    {
        //
    }
}
