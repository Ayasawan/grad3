<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Http\Resources\TransactionResource;
use App\Models\Admin;
use App\Models\Project;
use App\Models\Receipt;
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


    //admin_user
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

        $data = [];
        foreach ($transactions as $transaction) {
            $receipt = Receipt::where('transaction_id', $transaction->id)->first();
            $data[] = [
                'transaction' => new TransactionResource($transaction),
                'receipt' => $receipt ? $receipt->image : null,
            ];
        }

        return response()->json([
            'status' => 'success',
            'data' => $data,
        ], 200);
    }



    public function index()
    {
        $Transaction =  TransactionResource::collection(Transaction::get());
        return $this->apiResponse($Transaction, 'ok', 200);
    }


    //
    public function store(Request $request)
    {
        $admin = Auth::user();

        $input = $request->all();
        $validator = Validator::make($input, [
            'name' => 'required',
            'description' => 'required',
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
            'description' => $request->description,
            'price' => $request->price,
            'discount' => $request->discount,
            'user_id' => $user->id,
            'status' => 'Available',
        ]);

        if ($transaction) {
            // استرداد رقم حساب البنك للمشرف
            $adminBankAccountNumber = $admin->bank_account_number;

            $transactionData = $transaction->toArray();
            $transactionData['admin_bank_account_number'] = $adminBankAccountNumber;


            //notification
            $title = 'معاملة جديدة لمشروعك';
            $body = "عزيزي/عزيزتي {$user->first_name}، نود إعلامك بأنه تمت إضافة معاملة جديدة لمشروعك {$project->name}. يمكنك الآن طلب المعاملة الجديدة ومعالجتها. نرجو منك مراجعة التفاصيل واتخاذ الإجراءات اللازمة. .";
            $this->sendNotificationAndStore($project->user->id, 'user', $title, $body);


            return $this->apiResponse($transactionData, 'The transaction saved', 201);
        }

        return $this->apiResponse(null, 'Failed to save the transaction', 400);
    }


//ADMIN_user
    public function show( $id)
    {
        $Transaction= Transaction::find($id);
        if($Transaction){
            return $this->apiResponse(new  TransactionResource($Transaction) , 'ok' ,200);
        }
        return $this->apiResponse(null ,'the Transaction not found' ,404);
    }



    //admin
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

//admin
    public function destroy( $id)
    {
        $Transaction = Transaction::find($id);

        if(!$Transaction){
            return $this->apiResponse(null, 'This Transaction not found', 404);
        }

        $Transaction->delete($id);
        return $this->apiResponse(null, 'This Transaction deleted', 200);
    }


//admin
    public function reviewRequests()
    {
        $reviewRequests = Transaction::where('status', 'pending')->get();

        $data = [];
        foreach ($reviewRequests as $reviewRequest) {
            $receipt = Receipt::where('transaction_id', $reviewRequest->id)->first();
            $data[] = [
                'transaction' => new TransactionResource($reviewRequest),
                'receipt' => $receipt ? $receipt->image : null,
            ];
        }

        return $this->apiResponse($data, 'Review requests retrieved successfully', 200);
    }



//admin
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




//admin
    public function showAcceptedTransactions()
    {
        $approvedTransactions = Transaction::where('status', 'approved')->get();

        return $this->apiResponse(TransactionResource::collection($approvedTransactions), 'Approved transactions retrieved successfully', 200);
    }

    public function index_user($projectId)
    {
        $user = Auth::user();

        // استرداد مشروع محدد
        $project = $user->projects()->where('id', $projectId)->first();

        if (!$project) {
            return response()->json([
                'status' => 'error',
                'message' => 'لم يتم العثور على المشروع.',
            ], 404);
        }

        // استرداد المعاملات المرتبطة بالمشروع المحدد ذات الحالة "قيد المعالجة" أو "موافق عليها"
        $transactions = Transaction::where('project_id', $project->id)
            ->whereIn('status', ['pending', 'approved'])
            ->get();

        if ($transactions->isEmpty()) {
            return response()->json([
                'status' => 'success',
                'message' => 'لا توجد معاملات قيد المعالجة او موافق عليها لهذا المشروع.',
                'data' => [],
            ], 200);
        }

        $data = [];
        foreach ($transactions as $transaction) {
            $data[] = new TransactionResource($transaction);
        }

        return response()->json([
            'status' => 'success',
            'data' => $data,
        ], 200);
    }


    //admin
    public function reviewRequest($id)
    {
        $reviewRequest = Transaction::where('status', 'pending')->find($id);

        if (!$reviewRequest) {
            return $this->apiResponse([], 'Review request not found', 404);
        }

        $receipt = Receipt::where('transaction_id', $reviewRequest->id)->first();

        $data = [
            'transaction' => new TransactionResource($reviewRequest),
            'receipt' => $receipt ? $receipt->image : null,
        ];

        return $this->apiResponse($data, 'Review request retrieved successfully', 200);
    }
}

