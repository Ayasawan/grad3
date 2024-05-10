<?php

namespace App\Http\Controllers;
use App\Models\Interest;
use App\Traits\ApiResponseTrait;

use App\Models\Investor;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\InvestorResource;
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
    public function show($id)
    {
        $Investor= Investor::find($id);
        if($Investor){
            return $this->apiResponse(new InvestorResource($Investor) , 'ok' ,200);
        }
        return $this->apiResponse(null ,'the Investor not found' ,404);

    }

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
        return $this->apiResponse(null, 'This Investor deleted', 200);
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
