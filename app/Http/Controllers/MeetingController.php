<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;

use App\Models\Meeting;
use App\Models\Appointment;
use App\Models\Investor;
use App\Models\User;
use App\Models\Project;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\MeetingResource;
use App\Http\Resources\AppointmentResource;
use App\Http\Resources\ProjectResource;

use App\Traits\ApiResponseTrait;

class MeetingController extends Controller
{
    use  ApiResponseTrait;


    public function indexUser()
    {
        $userId = auth()->user()->id;
        $todayDate = now()->toDateString();
    
        $meetings = Meeting::where('user_id', $userId)
                            ->where('status_meeting', 0)
                            ->where('meeting_date', $todayDate)
                            ->get();
    
        $detailedMeetings = [];
        foreach ($meetings as $meeting) {
            $appointment = Appointment::find($meeting->appointment_id);
            $project = Project::find($meeting->project_id);
    
            $meeting->appointment = $appointment;
            $meeting->project = $project;
    
            $detailedMeetings[] = $meeting;
        }
    
        return $this->apiResponse($detailedMeetings, 'ok', 200);
    }




    public function indexAdmin()
    {
        $todayDate = now()->toDateString();
        $meetings = Meeting::where('status_meeting', 1)
                            ->where('meeting_date', $todayDate) // ترتيب تنازلي حسب تاريخ الاجتماع
                            ->get(); 
    
        $meeting = MeetingResource::collection($meetings);
        
        return $this->apiResponse($meeting, 'ok', 200);
    }

   
    
    public function store(Request $request, $id, $project_id)
    {
        $appointment = Appointment::find($id);
    
        if (!$appointment) {
            return $this->apiResponse(null, 'عذرًا، هذا الموعد غير متاح للحجز', 404);
        }
        if ($appointment->status_hour == 1) {
            return $this->apiResponse(null, 'عذرًا، هذا الموعد محجوز', 400);
        }
        
    
        $project = Project::find($project_id);
        if (!$project) {
            return $this->apiResponse(null, 'عذرًا، لم يتم العثور على المشروع المحدد', 404);
        }

    
        if ($project->accept_status == 1) {
            $user = $project->user_id;
            // Check if there is a pending meeting for the user in the Meeting table
            $existingMeeting = Meeting::where('user_id', $user)
                                        ->where('status_meeting', 0)
                                        ->first();
        
            if (!$existingMeeting) {
                $meeting = Meeting::create([
                    'user_id' => $user,
                    'investor_id' => Auth::id(),
                    'status_meeting' => 0,
                    'appointment_id' => $id,
                    'project_id' => $project_id, 
                    'meeting_date' => now()->toDateString(),
                ]);

                $appointment->status_hour = 1;
                $appointment->save();
        
                if ($meeting) {
                     // Send notification to investor
                        $investor = Investor::find($meeting->investor_id);
                        if ($investor) {
                            $title = ' انشاء اجتماع';
                            $body =   "تم إرسال طلب الاجتماع بنجاح. سنقوم بإعلامك فور تأكيد تواجد صاحب المشروع في الوقت المحدد.";
                            $this->sendNotificationAndStore($investor->id, 'investor', $title, $body);
                        }


                        $user = User::find($meeting->user_id);
                        if ($user) {
                            $title = ' انشاء اجتماع';
                            $body = "المستثمر {$investor->first_name} يود إقامة اجتماع لمناقشة المشروع. هل يمكنك تأكيد تواجدك في هذا الوقت؟";
                            $this->sendNotificationAndStore($user->id, 'user', $title, $body);
                        }

                        return $this->apiResponse(new MeetingResource($meeting), 'تم إرسال طلب الاجتماع بنجاح. سنقوم بإعلامك فور تأكيد تواجد صاحب المشروع في الوقت المحدد', 201);
                } else {
                    return $this->apiResponse(null, 'لم يتم حفظ الاجتماع', 400);
                }
            } else {
                return $this->apiResponse(null, 'لا يمكنك إنشاء اجتماع جديد، يوجد طلب اجتماع قيد الانتظار بالفعل', 400);
            }
        } else {
            return $this->apiResponse(null, 'لم يتم قبول المشروع من قبل الإداري بعد', 400);
        }

    }


    /**
     * Display the specified resource.
     */
    public function show(Meeting $meeting)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Meeting $meeting)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Meeting $meeting)
    {
        //
    }




    public function accept($id)
    {
        $meeting = Meeting::find($id);
    
        if (!$meeting) {
            return response()->json(['message' => 'لم يتم العثور على طلب الاجتماع.'], 404);
        }


        $user_token = Auth::user(); // احصل على معرف المستخدم المسجل الحالي

        if ($meeting->user_id == $user_token->id){

            if ($meeting->status_meeting == 1){

                return response()->json(['message' => 'عذرا ، تم قبول هذا الطلب مسبقا '], 404);
            }
            if ($meeting->status_meeting == 2){

                return response()->json(['message' => 'عذرا ، تم إلغاء هذا الطلب مسبقا '], 404);
            }

            $meeting->status_meeting = 1;
            $meeting->save();

            $appointment = Appointment::where('id', $meeting->appointment_id)->first();
            if ($appointment) {
                $appointment->status_hour = 1;
                $appointment->save();
            }
        }
        else {
            return response()->json(['message' => 'لست المستخدم المصرح به لقبول الاجتماع'], 404);
        }

            // Send notification to user
            $user = User::find($meeting->user_id);
            if ($user) {
                $title = 'تم قبول طلب الاجتماع';
                $body = "شكرًا لتلبية طلب الاجتماع {$user->first_name}. يُمكنك الانضمام عند حلول موعد الاجتماع.";
                $this->sendNotificationAndStore($user->id, 'user', $title, $body);
            }


              // Send notification to investor
              $investor = Investor::find($meeting->investor_id);
              if ($investor) {
                $title = 'تم قبول طلب الاجتماع';
                $body = "تم قبول طلب الاجتماع الخاص بك. نحن في انتظارك للانضمام عندما يحين موعد الاجتماع.";
                $this->sendNotificationAndStore($investor->id, 'investor', $title, $body);
              }


              return response()->json(['message' => 'تم قبول طلب الاجتماع بنجاح.']);

    }


}
