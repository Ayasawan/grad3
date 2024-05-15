<?php
namespace App\Http\Controllers;

use App\Models\Interest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use App\Http\Resources\InterestResource;
use Illuminate\Support\Facades\Validator;

class InterestController extends Controller
{
    use ApiResponseTrait;

    public function index()
    {
        $interests = InterestResource::collection(Interest::get());
        return $this->apiResponse($interests, 'ok', 200);
    }

    public function store(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'name' => 'required|unique:interests',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(null, $validator->errors(), 400);
        }

        $interest = Interest::create([
            'name' => $request->name,
        ]);

        if ($interest) {
            return $this->apiResponse(new InterestResource($interest), 'The interest is saved', 201);
        }

        return $this->apiResponse(null, 'The interest is not saved', 400);
    }

    public function show($id)
    {
        $interest = Interest::find($id);

        if ($interest) {
            return $this->apiResponse(new InterestResource($interest), 'ok', 200);
        }

        return $this->apiResponse(null, 'The interest is not found', 404);
    }

    public function update(Request $request, $id)
    {
        $interest = Interest::find($id);

        if (!$interest) {
            return $this->apiResponse(null, 'The interest is not found', 404);
        }

        $input = $request->all();
        $validator = Validator::make($input, [
            'name' => 'required|unique:interests,name,' . $id,
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(null, $validator->errors(), 400);
        }

        $interest->update($request->all());

        return $this->apiResponse(new InterestResource($interest), 'The interest is updated', 200);
    }

    public function destroy($id)
    {
        $interest = Interest::find($id);

        if (!$interest) {
            return $this->apiResponse(null, 'The interest is not found', 404);
        }

        $interest->delete();

        return $this->apiResponse(null, 'The interest is deleted', 200);
    }

}


