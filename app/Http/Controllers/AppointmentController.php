<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\AppointmentResource;
use App\Traits\ApiResponseTrait;

class AppointmentController extends Controller
{
    use  ApiResponseTrait;
    /**
     * Display a listing of the resource.
     */
    public function indexAdmin()
    {
        $appointment = AppointmentResource::collection(Appointment::get());
        return $this->apiResponse($appointment, 'ok', 200);
    }



    public function indexInvestor()
    {
        $todayDate = now()->toDateString();
    
        $appointments = Appointment::where('date', $todayDate)->where('status_hour', 0)->get();
        $appointmentResource = AppointmentResource::collection($appointments);
    
        return $this->apiResponse($appointmentResource, 'ok', 200);
    }
    

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'hour' => ['required', 'regex:/^([1-9]|1[0-2]):([0-5][0-9])$/', 'unique:appointments,hour'],
            'time_period' => 'required',
        ]);
        
        
    
        if ($validator->fails()) {
            return $this->apiResponse(null, $validator->errors(), 400);
        }
    
        $data = [
            'hour' => $request->hour,
            'status_hour' => 0,
            'time_period' => $request->time_period, // استخدام قيمة time_period من مدخلات الطلب
            'date' => now()->toDateString(),
        ];
    
        $appointment = Appointment::create($data);
    
        if ($appointment) {
            return $this->apiResponse(new AppointmentResource($appointment), 'The appointment has been saved', 201);
        }
    
        return $this->apiResponse(null, 'Failed to save the appointment', 400);
    }
    







    /**
     * Display the specified resource.
     */
    public function show($id)
    {

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,  $id)
    {
        $appointment= Appointment::find($id);
        if(!$appointment)
        {
            return $this->apiResponse(null ,'the Appointment not found ',404);
        }
        $appointment->update($request->all());
        if($appointment)
        {
            return $this->apiResponse(new AppointmentResource($appointment) , 'the appointment was updated',201);

        }
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy( $id)
    {
        $Appointment =  Appointment::find($id);

        if(!$Appointment){
            return $this->apiResponse(null, 'This Appointment not found', 404);
        }

        $Appointment->delete($id);
            return $this->apiResponse(null, 'This Appointment deleted', 200);
    }
}
