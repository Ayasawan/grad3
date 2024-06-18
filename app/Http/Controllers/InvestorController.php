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

        // Retrieve all projects associated with the investor
        $projects = Project::where('investor_id', $user->id)->get();

        // Include investor information along with associated projects
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

        // Retrieve all projects associated with the investor
        $projects = Project::where('investor_id', $id)->get();

        // Prepare data to be returned
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

}



