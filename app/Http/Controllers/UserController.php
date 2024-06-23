<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProjectResource;
use App\Traits\ApiResponseTrait;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    use ApiResponseTrait;

//user

    public function indexUser()
    {
        $User = UserResource::collection(User::get());
        return $this->apiResponse($User, 'ok', 200);
    }



    public function showForAdminUser($id)
    {
        $user = User::find($id);

        if (!$user) {
            return $this->apiResponse(null, 'User not found', 404);
        }

        $projects = $user->projects;

        $data = [
            'user' => new UserResource($user),
            'projects' => ProjectResource::collection($projects)
        ];

        return $this->apiResponse($data, 'ok', 200);
    }

    public function showMyProfileUser()
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json("User not authenticated", 401);
        }

        $id = $user->id;
        $userProjects = User::with('projects')->find($id);

        $investedProjects = $userProjects->projects->where('investment_status', 1);
        $pendingProjects = $userProjects->projects->where('investment_status', 0);

        $responseData = [
            'user' => $user,
            'invested_projects' => $investedProjects->values(), 
            'pending_projects' => $pendingProjects->values(),
        ];

        return $this->apiResponse($responseData, 'ok', 200);
    }



    public function showProfileByAnotherUser($id)
    {
        $user = User::find($id);

        if (!$user) {
            return $this->apiResponse(null, 'The user was not found', 404);
        }

        // Retrieve all projects associated with the user
        $projects = $user->projects;

        // Prepare data to be returned
        $data = [
            'user' => [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'user_type' => $user->user_type,
                'location' => $user->location,
            ],
            'projects' => $projects->map(function ($project) {
                return [
                    'id' => $project->id,
                    'name' => $project->name,
                    'description' => $project->description,
                    'location' => $project->location,
                    'type_id' => $project->type_id,
                ];
            }),
        ];

        return $this->apiResponse($data, 'ok', 200);
    }




    public function updateuser(Request $request,  $id)
    {
        $User= User::find($id);
        if(!$User)
        {
            return $this->apiResponse(null ,'the User not found ',404);
        }

        $User->update($request->all());
        if($User) {
            return $this->apiResponse(new UserResource($User), 'the User update', 201);

        }}



    //user
    public function destroyUser(Request $request)
    {
        $email = $request->input('email');
        $password = $request->input('password');

        $user = User::where('email', $email)->first();

        if (!$user) {
            return $this->apiResponse(null, 'This user not found', 404);
        }

        // قم بإجراء التحقق من صحة كلمة المرور
        if (!Hash::check($password, $user->password)) {
            return $this->apiResponse(null, 'Invalid password', 401);
        }

        $user->delete();
        return $this->apiResponse(null, 'This user deleted', 200);
    }

    public function destroyAdmin( $id)
    {
        $User =  User::find($id);

        if(!$User){
            return $this->apiResponse(null, 'This User not found', 404);
        }

        $User->delete($id);
        return $this->apiResponse(null, 'This User deleted', 200);
    }
}
