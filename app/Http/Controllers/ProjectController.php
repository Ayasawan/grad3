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

    public function index()
    {
        $Project = ProjectResource::collection(Project::get());
        return $this->apiResponse($Project, 'ok', 200);
    }

    public function store(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'description' => 'required',
            'feasibility_study' => 'required',
            'amount' => 'required',
            'location' => 'required',
            'type_id' => 'required',
            'interests' => 'required|array', // مصفوفة من الاهتمامات المختارة
            'interests.*' => 'integer', // تأكيد أن قيم الاهتمامات هي أعداد صحيحة
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(null, $validator->errors(), 400);
        }

        if ($request->hasFile('feasibility_study')) {
            $file = $request->file('feasibility_study');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('feasibility_study/Project'), $fileName);
        }

        $projectData = [
            'description' => $request->description,
            'feasibility_study' => $fileName,
            'amount' => $request->amount,
            'location' => $request->location,
            'investor_id' => '1',
            'user_id' => Auth::id(),
            'type_id' => $request->type_id,
        ];

        $project = Project::create($projectData);

        if ($project) {
            $interests = $request->interests;
            $project->interests()->attach($interests);

            // استعادة المشروع بما في ذلك الاهتمامات المرتبطة به
            $projectWithInterests = Project::with('interests')->find($project->id);

            return $this->apiResponse(new ProjectResource($projectWithInterests), 'تم حفظ المشروع', 201);
        }

        return $this->apiResponse(null, 'لم يتم حفظ المشروع', 400);
    }
//    public function store(Request $request)
//    {
//
//        $input=$request->all();
//        $validator = Validator::make( $input, [
//            'description' => 'required',
//            'feasibility_study' => 'required',
//            'amount' => 'required',
//            'location' => 'required',
//            'type_id' => 'required',
//            'interests' => 'required|array', // مصفوفة من الاهتمامات المختارة
//        ]);
//        if ($validator->fails()) {
//            return $this->apiResponse(null, $validator->errors(), 400);
//        }
//
//
//        if ($request->hasFile('feasibility_study')) {
//            $file = $request->file('feasibility_study');
//            $fileName = time() . '_' . $file->getClientOriginalName();
//            $file->move(public_path('feasibility_study/Project'), $fileName);
//        }
//
//
//        $Project = Project::query()->create([
//            'description' => $request->description,
//            'feasibility_study' => $fileName,
//            'amount' => $request->amount,
//            'location' => $request->location,
//            'investor_id' => '1',
//            'user_id' => Auth::id(),
//            'type_id' => $request->type_id,
//        ]);
//        // ربط الاهتمامات بالمشروع
//        $Project->interests()->sync($request->interests);
//
//        if ($Project) {
//            return $this->apiResponse(new ProjectResource($Project), 'the Project save', 201);
//        }
//        return $this->apiResponse(null, 'the Project  not save', 400);
//    }
//

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


}

