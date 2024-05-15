<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use App\Http\Resources\ProjectResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;




class ProjectController extends Controller
{
    use  ApiResponseTrait;

    public function indexAdmin()
    {
        $Project = ProjectResource::collection(Project::where('accept_status', 0)->get());
        return $this->apiResponse($Project, 'ok', 200);
    }



    public function indexPublic()
    {
        $Project = ProjectResource::collection(Project::where('accept_status', 1)->get());
        return $this->apiResponse($Project, 'ok', 200);
    }





    public function store(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'name' => 'required',
            'description' => 'required',
            'feasibility_study' => 'required',
            'amount' => 'required',
            'location' => 'required',
            'type_id' => 'required',
            'interests' => 'required|array', // مصفوفة من الاهتمامات المختارة
            'interests.*' => 'integer', // تأكيد أن قيم الاهتمامات هي أعداد صحيحة
            // New fields for user information
            'iD_card' => ['nullable',],
            'personal_photo' => ['nullable',],
            'property_deed' => ['nullable',],
            'clean_record' => ['nullable',],
        ]);


        // Upload user images
        $IDCardFile=$this->saveImage($request->iD_card,'images/user');
        $personalPhotoFile=$this->saveImage($request->personal_photo,'images/user');
        $propertyDeedFile=$this->saveImage($request->property_deed,'images/user');
        $cleanRecordFile=$this->saveImage($request->clean_record,'images/user');


        if ($validator->fails()) {
            return $this->apiResponse(null, $validator->errors(), 400);
        }



        if ($request->hasFile('feasibility_study')) {
            $file = $request->file('feasibility_study');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('feasibility_study/Project'), $fileName);
        }



        $projectData = [
            'name' => $request->name,
            'description' => $request->description,
            'feasibility_study' => $fileName,
            'amount' => $request->amount,
            'location' => $request->location,
            'investment_status' => false,
            'accept_status' => false,

            'investor_id' => '1',
            'user_id' => Auth::id(),
            'type_id' => $request->type_id,
        ];

        $project = Project::create($projectData);

          // Update user's record with uploaded images
          $user = auth()->user();
          $userData = [
              'iD_card' => $IDCardFile,
              'personal_photo' => $personalPhotoFile,
              'property_deed' => $propertyDeedFile,
              'clean_record' =>  $cleanRecordFile,

          ];
          $user->update($userData);


        if ($project) {
            $interests = $request->interests;
            $project->interests()->attach($interests);

            // استعادة المشروع بما في ذلك الاهتمامات المرتبطة به
            $projectWithInterests = Project::with('interests')->find($project->id);

            return $this->apiResponse(new ProjectResource($projectWithInterests), 'تم حفظ المشروع', 201);
        }

        return $this->apiResponse(null, 'لم يتم حفظ المشروع', 400);
    }


    public function show( $id)
    {
        $Project= Project::find($id);
        if($Project){
            return $this->apiResponse(new ProjectResource($Project) , 'ok' ,200);
        }
        return $this->apiResponse(null ,'the Project not found' ,404);
    }







    public function update(Request $request,  $id)
    {
        $Project= Project::find($id);
        if(!$Project)
        {
            return $this->apiResponse(null ,'the Project not found ',404);
        }
        $Project->update($request->all());
        if($Project)
        {
            return $this->apiResponse(new ProjectResource($Project) , 'the Project update',201);

        }
    }


    public function destroy( $id)
    {
        $Project =  Project::find($id);

        if(!$Project){
            return $this->apiResponse(null, 'This Project not found', 404);
        }

        $Project->delete($id);
            return $this->apiResponse(null, 'This Project deleted', 200);
    }


    public function searchByName($name)
    {
        $projects = Project::where(function ($query) use ($name) {
            $query->where("name", "LIKE", "%" . $name . "%")
                ->orWhereRaw("SOUNDEX(name) = SOUNDEX('" . $name . "')");
        })->get();

        if ($projects) {
            return $this->apiResponse($projects, 'ok', 200);
        }
    }

    public function searchByAmount($amount)
    {
        $projects = Project::where("amount", "<=", $amount)->get();
        if ($projects) {
            return $this->apiResponse($projects, 'ok', 200);
        }
    }




    public function acceptProject($id)
    {
        $project = Project::find($id);

        if (!$project) {
            return response()->json(['message' => 'لم يتم العثور على المشروع.'], 404);
        }

        $project->accept_status = 1;
        $project->save();

        return response()->json(['message' => 'تم قبول المشروع بنجاح.']);
    }



}

