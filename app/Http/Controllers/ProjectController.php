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
        
        $input=$request->all();
        $validator = Validator::make( $input, [
            'description' => 'required',
            'feasibility_study' => 'required',
            'amount' => 'required',
            'location' => 'required',
            'investor_id' => 'required',
            'type_id' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->apiResponse(null, $validator->errors(), 400);
        }
        $Project = Project::query()->create([
            'description' => $request->description,
            'feasibility_study' => $request->feasibility_study,
            'amount' => $request->amount,
            'location' => $request->location,
            'investor_id' => $request->investor_id,
            'user_id' => Auth::id(),
            'type_id' => $request->type_id,
        ]);

        if ($Project) {
            return $this->apiResponse(new ProjectResource($Project), 'the Project save', 201);
        }
        return $this->apiResponse(null, 'the Project  not save', 400);
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
        if($Project->user_id !=Auth::id()){
            return $this->apiResponse(null, 'you do not have rights', 400);
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
