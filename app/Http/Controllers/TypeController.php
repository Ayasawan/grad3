<?php

namespace App\Http\Controllers;
use App\Traits\ApiResponseTrait;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\TypeResource;
use App\Models\Type;

class TypeController extends Controller
{

    use ApiResponseTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $type = TypeResource::collection(Type::get());
        return $this->apiResponse($type, 'ok', 200);
    }

    

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $input=$request->all();
        $validator = Validator::make($input , [
            'name'=>'required',
           
        ]);
        if ($validator->fails()){
            return $this->apiResponse(null,$validator ->errors() , 400);
        }

        $type = Type::query()->create([
            'name' => $request->name,
        ]);
        if($type) {
            return $this->apiResponse(new TypeResource($type), 'This type saved', 201);
        }
        return $this->apiResponse(null, 'This type not save', 400);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $type= Type::find($id);
        if($type){
            return $this->apiResponse(new TypeResource($type) , 'ok' ,200);
        }
        return $this->apiResponse(null ,'the type not found' ,404);

    }

   /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,  $id)
    {
        $type= Type::find($id);
        if(!$type)
        {
            return $this->apiResponse(null ,'the type not found ',404);
        }
        $type->update($request->all());
        if($type)
        {
            return $this->apiResponse(new TypeResource($type) , 'the type was updated',201);

        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        
        $type = Type::find($id);
        if(!$type)
        {
            return $this->apiResponse(null ,'the type not found ',404);
        }
        $type->delete($id);
        if($type)
            return $this->apiResponse(null ,'the type was deleted',200);
    }


    public function destroy14(string $id)
    {
        
        $type = Type::find($id);
        if(!$type)
        {
            return $this->apiResponse(null ,'the type not found ',404);
        }
        $type->delete($id);
        if($type)
            return $this->apiResponse(null ,'the type was deleted',200);
    }

        public function showProjectsByType(string $typeId)
    {
        // Find the requested type
        $type = Type::find($typeId);

        // Check if the type exists
        if ($type) {
            // Get the projects associated with the specified type
            $projects = $type->projects;

            // Return the projects as a response
            return $this->apiResponse($projects, 'List of projects for this type', 200);
        } else {
            // If the type does not exist
            return $this->apiResponse(null, 'Type not found', 404);
        }
    }

}
