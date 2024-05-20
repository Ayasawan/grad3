<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReceiptResource;
use App\Http\Resources\TransactionResource;
use App\Models\Receipt;
use App\Models\Transaction;
use App\Models\User;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ReceiptController extends Controller
{
    use  ApiResponseTrait;


    public function requestTransaction(Request $request, $id)
    {
        $user = Auth::user();
        $user_id = $user->id;

        $transaction = Transaction::find($id);
        if (!$transaction) {
            return $this->apiResponse(null, 'Transaction not found', 404);
        }

        $project = $user->projects()->where('id', $transaction->project_id)->first();
        if (!$project) {
            return $this->apiResponse(null, 'Unauthorized', 401);
        }

        // التحقق مما إذا كان المستخدم الحالي هو نفس المستخدم الذي أضاف المشروع
        if ($project->user_id !== $user_id) {
            return $this->apiResponse(null, 'Unauthorized', 401);
        }

        $validator = Validator::make($request->all(), [
            'image' => ['nullable', 'image'],
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(null, $validator->errors(), 400);
        }

        $file_name = null;
        if ($request->hasFile('image')) {
            $file_name = $this->saveImage($request->file('image'), 'images/Transaction');
            if (!$file_name) {
                return $this->apiResponse(null, 'Failed to save the image', 500);
            }
        }

        // قم بتحديث حالة المعاملة إلى "قيد المراجعة" إذا لم تكن بالفعل قيد المراجعة
        if ($transaction->status !== 'pending') {
            $transaction->status = 'pending';
            $transaction->save();
            $statusMessage = 'Transaction requested successfully. Please wait for processing.';
        } else {
            $statusMessage = 'Transaction is already under processing.';
        }

        // حفظ الصورة و transaction_id في جدول الإيصالات
        $receipt = Receipt::create([
            'transaction_id' => $transaction->id,
            'image' => $file_name,
        ]);

        return $this->apiResponse(new TransactionResource($transaction), $statusMessage, 200);
    }

}
