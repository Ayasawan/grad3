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
   
   //for_admin
     public function index()
     {
         $Communication = CommunicationResource::collection(Communication::get());
         return $this->apiResponse($Communication, 'ok', 200);
     }

    //for_investor
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

                // إرسال الإشعار
            $title = ' طلب تواصل';
            $body = "عزيزي {$investor->first_name}، تم تقديم طلب التواصل بنجاح لمشروع '{$project->name}'. سيتم معالجته في أقرب وقت ممكن. سيتم اعلامك بأي تحديثات إضافية.";
            $this->sendNotificationAndStore($investor->id, 'investor', $title, $body);

            return $this->apiResponse(new CommunicationResource($communication), 'تم حفظ طلب التواصل والمعلومات الإضافية', 201);
        }

        return $this->apiResponse(null, 'فشل في حفظ طلب التواصل', 400);
    }
            


    //for_admin
    public function show($id)
    {
        $communication = Communication::with(['project.user', 'investor'])->find($id);
    
        if (!$communication) {
            return response()->json(['message' => 'لم يتم العثور على طلب التواصل.'], 404);
        }
    
        return response()->json(['communication' => $communication]);
    }
    


    //for_admin
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

        // Send notification to investor
        $investor = Investor::find($communication->investor_id);
        if ($investor) {
            $title = 'قبول طلب التواصل';
            $body = "عزيزي/عزيزتي {$investor->first_name}، تم قبول طلب التواصل الخاص بك لمشروع '{$project->name}' من قبل الفريق المختص. سيتم التواصل معك في أقرب وقت ممكن لمتابعة المشروع.";
            $this->sendNotificationAndStore($investor->id, 'investor', $title, $body);
        }
        
        return response()->json(['message' => 'Communication request accepted successfully.']);
    }
    
  

}
