<?php

namespace App\Http\Controllers;
use App\Http\Resources\UserResource;
use App\Models\Interest;
use App\Models\User;
use App\Traits\ApiResponseTrait;

use App\Models\Investor;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\InvestorResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Hash;


class InvestorController extends Controller
{
    use ApiResponseTrait;

    public function index()
    {
        $Investor = InvestorResource::collection(Investor::get());
        return $this->apiResponse($Investor, 'ok', 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function showMyProfile()

    {
        $user = auth()->user();

        if (!$user) {
            return response()->json ("User not authenticated", 401);
        }
        // $Investor= Investor::find($id);
        $id =$user->id;

        $Investor= Investor::find($id);

        if($Investor){
            return $this->apiResponse(new InvestorResource($Investor) , 'ok' ,200);
        }
        return $this->apiResponse(null ,'the Investor not found' ,404);

    }



    public function showProfileByAnother($id)
{
    $investor = Investor::find($id);

    if ($investor) {
        $data = [
            'first_name' => $investor->first_name,
            'last_name' => $investor->last_name,
            'user_type' => $investor->user_type,
            'location' => $investor->location,
        ];

        return $this->apiResponse($data, 'ok', 200);
    }

    return $this->apiResponse(null, 'The investor was not found', 404);
}



public function showForAdmin($id)
{
    $User = User::find($id);

    if ($User) {
        return $this->apiResponse(new UserResource($User), 'ok', 200);
    }

    return $this->apiResponse(null, 'The User was not found', 404);
}
//user
    public function indexUser()
    {
        $User = UserResource::collection(User::get());
        return $this->apiResponse($User, 'ok', 200);
    }
    public function destroyAdminUser( $id)
    {
        $User =  User::find($id);

        if(!$User){
            return $this->apiResponse(null, 'This User not found', 404);
        }

        $User->delete($id);
        return $this->apiResponse(null, 'This User deleted', 200);
    }

    public function showMyProfileUser()

    {
        $user = auth()->user();

        if (!$user) {
            return response()->json ("User not authenticated", 401);
        }
        // $Investor= Investor::find($id);
        $id =$user->id;

        $User= User::find($id);

        if($User){
            return $this->apiResponse(new UserResource($User) , 'ok' ,200);
        }
        return $this->apiResponse(null ,'the User not found' ,404);

    }



    public function showProfileByAnotherUser($id)
    {
        $User= User::find($id);

        if ($User) {
            $data = [
                'first_name' => $User->first_name,
                'last_name' => $User->last_name,
                'user_type' => $User->user_type,
                'location' => $User->location,
            ];

            return $this->apiResponse($data, 'ok', 200);
        }

        return $this->apiResponse(null, 'The investor was not found', 404);
    }



    public function showForAdminUser($id)
    {
        $User = User::find($id);

        if ($User) {
            return $this->apiResponse(new InvestorResource($User), 'ok', 200);
        }

        return $this->apiResponse(null, 'The User was not found', 404);
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
//
//    public function destroyAdminUser( $id)
//    {
//        $User =  User::find($id);
//
//        if(!$User){
//            return $this->apiResponse(null, 'This User not found', 404);
//        }
//
//        $User->delete($id);
//        return $this->apiResponse(null, 'This User deleted', 200);
//    }

    /**
     * Update the specified resource in storage.
     */
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





    /**
     * Remove the specified resource from storage.
     */
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

    public function addInterests(Request $request, $investorId)
    {
        $validatedData = $request->validate([
            'interests' => 'required|array',
            'interests.*' => 'integer|exists:interests,id',
        ]);

        $investor = Investor::find($investorId);

        if (!$investor) {
            return response()->json(['message' => 'Investor not found'], 404);
        }

        $investor->interests()->syncWithoutDetaching($validatedData['interests']);

        $addedInterests = Interest::whereIn('id', $validatedData['interests'])->get();

        return response()->json(['message' => 'Interests added successfully', 'interests' => $addedInterests], 200);
    }
}
