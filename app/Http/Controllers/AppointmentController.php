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
    public function index()
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
        'hours' => 'required|array',
        'hours.*' => [
            'required',
            'string',
            'regex:/^([1-9]|1[0-2]):([0-5][0-9])$/'
        ] 
    ]);

    if ($validator->fails()) {
        return $this->apiResponse(null, $validator->errors(), 400);
    }

    $hours = $request->input('hours');
    $updatedAppointments = [];

    try {
        // حذف المواعيد القديمة
        Appointment::whereNotIn('hour', $hours)->delete();

        // إضافة المواعيد الجديدة مع تحديد AM أو PM
        foreach ($hours as $key => $hour) {
            $timePeriod = $request->input('time_period.' . $key);
            
            $createdAppointment = Appointment::updateOrCreate(
                ['hour' => $hour],
                [
                    'date' => now()->toDateString(), // تعيين التاريخ الحالي
                    'status_hour' => 0,
                    'time_period' => $timePeriod
                ]
            );
        
            $updatedAppointments[] = new AppointmentResource($createdAppointment);
        }

        return $this->apiResponse($updatedAppointments, 'Updated Appointments saved', 201);
    } catch (\Exception $e) {
        return $this->apiResponse(null, 'Error saving Appointments: ' . $e->getMessage(), 500);
    }
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
    public function update(Request $request, Appointment $appointment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Appointment $appointment)
    {
        //
    }
}
