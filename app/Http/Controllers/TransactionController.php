<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\TransactionResource;
use App\Models\Project;
use App\Models\Report;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class TransactionController  extends Controller
{
    use  ApiResponseTrait;

    public function indexx($projectId)
    {
        $project = Project::find($projectId);

        if (!$project) {
            return response()->json([
                'status' => 'error',
                'message' => 'Project not found',
            ], 404);
        }

        $transactions = Transaction::where('project_id', $projectId)->get();

        return response()->json([
            'status' => 'success',
            'data' => TransactionResource::collection($transactions),
        ], 200);}


    public function index()
    {
        $Transaction =  TransactionResource::collection(Transaction::get());
        return $this->apiResponse($Transaction, 'ok', 200);
    }
    public function store(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'name' => 'required',
            'price' => 'required',
            'discount' => 'required',
            'project_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(null, $validator->errors(), 400);
        }

        // استرداد المشروع المطلوب
        $project = Project::find($request->project_id);

        if (!$project) {
            return $this->apiResponse(null, 'Project not found', 404);
        }

        // استرداد المستخدم المرتبط بالمشروع
        $user = $project->user;

        $transaction = $project->transactions()->create([
            'name' => $request->name,
            'price' => $request->price,
            'discount' => $request->discount,
            'user_id' => $user->id,
            'status' => 'Available',
        ]);

        if ($transaction) {
            return $this->apiResponse(new TransactionResource($transaction), 'The transaction saved', 201);
        }

        return $this->apiResponse(null, 'Failed to save the transaction', 400);
    }
    public function show( $id)
    {
        $Transaction= Transaction::find($id);
        if($Transaction){
            return $this->apiResponse(new  TransactionResource($Transaction) , 'ok' ,200);
        }
        return $this->apiResponse(null ,'the Transaction not found' ,404);
    }


    public function update(Request $request,  $id)
    {
        $Transaction= Transaction::find($id);
        if(!$Transaction)
        {
            return $this->apiResponse(null ,'the Transaction not found ',404);
        }
        $Transaction->update($request->all());
        if($Transaction)
        {
            return $this->apiResponse(new  TransactionResource($Transaction) , 'the Transaction update',201);

        }
    }


    public function destroy( $id)
    {
        $Transaction = Transaction::find($id);

        if(!$Transaction){
            return $this->apiResponse(null, 'This Transaction not found', 404);
        }

        $Transaction->delete($id);
        return $this->apiResponse(null, 'This Transaction deleted', 200);
    }


    public function requestTransaction(Request $request, $id)
    {
        $user = Auth::user();

        $transaction = Transaction::find($id);

        if (!$transaction) {
            return $this->apiResponse(null, 'Transaction not found', 404);
        }

        // التحقق من أن المعاملة تنتمي إلى مشروع المستخدم الحالي
        $project = $user->projects()->where('id', $transaction->project_id)->first();

        if (!$project) {
            return $this->apiResponse(null, 'Unauthorized', 401);
        }

        // قم بتحديث حالة المعاملة إلى "قيد المراجعة"
        $transaction->status = 'pending'; // تعديل القيمة إلى "قيد المراجعة"
        $transaction->save();

        $statusMessage = 'Transaction requested successfully. Please wait for processing.';
        if ($transaction->status === 'pending') {
            $statusMessage = 'Transaction is already under processing.';
        }
        return $this->apiResponse(new TransactionResource($transaction), 'Transaction requested successfully', 200);
    }

    public function reviewRequests()
    {
        $reviewRequests = Transaction::where('status', 'pending')->get(); // تعديل القيمة إلى "قيد المراجعة"

        return $this->apiResponse(TransactionResource::collection($reviewRequests), 'Review requests retrieved successfully', 200);
    }

    public function approveTransaction(Request $request, $id)
    {
        $transaction = Transaction::find($id);

        if (!$transaction) {
            return $this->apiResponse(null, 'Transaction not found', 404);
        }

        // قم بتحديث حالة المعاملة إلى "تمت الموافقة"
        $transaction->status = 'approved'; // تعديل القيمة إلى "تمت الموافقة"
        $transaction->save();
        return $this->apiResponse(new TransactionResource($transaction), 'Transaction approved successfully', 200);
    }

    public function showAcceptedTransactions()
    {
        $approvedTransactions = Transaction::where('status', 'approved')->get();

        return $this->apiResponse(TransactionResource::collection($approvedTransactions), 'Approved transactions retrieved successfully', 200);
    }
    public function userTransactions()
    {
        $user = Auth::user();

        $transactions = Transaction::whereHas('project', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->get();

        return $this->apiResponse(TransactionResource::collection($transactions), 'User transactions retrieved successfully', 200);
    }
}

