<?php

namespace App\Http\Controllers;
use App\Http\Resources\UserResource;
use App\Models\Interest;
use App\Models\User;
use App\Models\Project;
use App\Http\Resources\ProjectResource;
use App\Traits\ApiResponseTrait;

use App\Models\Investor;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\InvestorResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Response;

class InvestorController extends Controller
{
    use ApiResponseTrait;

    public function index()
    {
        $Investor = InvestorResource::collection(Investor::get());
        return $this->apiResponse($Investor, 'ok', 200);
    }




    public function showMyProfile()
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json("User not authenticated", 401);
        }
        $investor = Investor::find($user->id);
        if (!$investor) {
            return $this->apiResponse(null, 'Investor not found', 404);
        }

        $projects = Project::where('investor_id', $user->id)->get();

        $data = [
            'investor' => new InvestorResource($investor),
            'projects' => ProjectResource::collection($projects)
        ];
        return $this->apiResponse($data, 'ok', 200);
    }



    public function showProfileByAnother($id)
    {
        $investor = Investor::find($id);
        if (!$investor) {
            return $this->apiResponse(null, 'The investor was not found', 404);
        }
        $projects = Project::where('investor_id', $id)->get();

        $data = [
            'investor' => [
                'first_name' => $investor->first_name,
                'last_name' => $investor->last_name,
                'user_type' => $investor->user_type,
                'location' => $investor->location,
            ],
            'projects' => $projects->map(function ($project) {
                return [
                    'name' => $project->name,
                    'description' => $project->description,
                    'location' => $project->location,
                    'type_id' => $project->type_id,
                ];
            }),
        ];

        return $this->apiResponse($data, 'ok', 200);
    }




    public function showForAdmin($id)
    {
        $investor = Investor::find($id);

        if (!$investor) {
            return $this->apiResponse(null, 'The investor was not found', 404);
        }

        // Retrieve all projects associated with the investor
        $projects = Project::where('investor_id', $id)->get();

        $data = [
            'investor' => new InvestorResource($investor),
            'projects' => ProjectResource::collection($projects)
        ];

        return $this->apiResponse($data, 'ok', 200);
    }


    public function update(Request $request,  $id)
    {
        $Investor= Investor::find($id);
        if(!$Investor)
        {
            return $this->apiResponse(null ,'the Investor not found ',404);
        }

        $Investor->update($request->all());
        if($Investor)
        {
            return $this->apiResponse(new InvestorResource($Investor) , 'the Investor update',201);

        }
    }

    public function destroyAdmin( $id)
    {
        $Investor =  Investor::find($id);

        if(!$Investor){
            return $this->apiResponse(null, 'This Investor not found', 404);
        }

        $Investor->delete($id);
        return $this->apiResponse(null, 'This Investor deleted', 200);
    }


    public function destroyInvestor(Request $request)
    {
        $email = $request->input('email');
        $password = $request->input('password');

        $Investor = Investor::where('email', $email)->first();

        if (!$Investor) {
            return $this->apiResponse(null, 'This Investor not found', 404);
        }

        // قم بإجراء التحقق من صحة كلمة المرور
        if (!Hash::check($password, $Investor->password)) {
            return $this->apiResponse(null, 'Invalid password', 401);
        }

        $Investor->delete();
        return $this->apiResponse(null, 'This user deleted', 200);
    }
/////////////////////////////////////////////////////////////




    public function addInterests(Request $request)
    {
        $validatedData = $request->validate([
            'interests' => 'required|array',
            'interests.*' => 'integer|exists:interests,id',
        ]);

        $investor = auth()->user(); // المستثمر المصادق عليه
        $investor_id = $investor->id;
        $investor1 =Investor::find($investor_id);

        if (!$investor1) {
            return response()->json(['message' => 'Investor not found'], 404);
        }

        $investor1->interests()->syncWithoutDetaching($validatedData['interests']);

        $addedInterests = Interest::whereIn('id', $validatedData['interests'])->get();

        return response()->json(['message' => 'Interests added successfully', 'interests' => $addedInterests], 200);
    }

    public function getProjectsByInvestorInterests(Request $request)
    {
        $investor = auth()->user();
        $investor_id = $investor->id;
        $investor1 =Investor::find($investor_id);

        if (!$investor1 || $investor1->interests()->count() === 0) {
            return response()->json(['message' => 'User is not authenticated or not an investor'], Response::HTTP_UNAUTHORIZED);
        }


        $interestIds = $investor1->interests->pluck('id')->toArray();

        $projects = Project::whereHas('interests', function ($query) use ($interestIds) {
            $query->whereIn('interests.id', $interestIds);
        })->get();

        return response()->json(['projects' => $projects], Response::HTTP_OK);
    }
}



