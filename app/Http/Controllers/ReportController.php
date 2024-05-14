<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Report;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use App\Http\Resources\ReportResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class ReportController  extends Controller
{
    use  ApiResponseTrait;

    public function index()
    {
        $Report = ReportResource::collection(Report::get());
        return $this->apiResponse($Report, 'ok', 200);
    }
    public function userReports()
{
    $userReports = Report::where('user_id', Auth::id())->get();

    if ($userReports->isEmpty()) {
        return $this->apiResponse(null, 'No reports found for the user', 404);
    }

    return $this->apiResponse(ReportResource::collection($userReports), 'OK', 200);
}

    public function projectReports($project_id)
    {
        $reports = Report::where('project_id', $project_id)->get();

        if ($reports->isEmpty()) {
            return $this->apiResponse(null, 'No reports found for the project', 404);
        }

        return $this->apiResponse(ReportResource::collection($reports), 'OK', 200);
    }

    public function store(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'عنوان_التقرير' => 'required',
            'ملخص_الأهداف_المحققة' => 'required',
            'ملخص_الأهداف_غير_المحققة' => 'required',
            'مبلغ_المستثمر' => 'required|numeric',
            'الإيرادات_الإجمالية' => 'required|numeric',
            'التكاليف_الإجمالية' => 'required|numeric',
            'الأرباح_الصافية' => 'required|numeric',
            'الصافي_الربح_لصاحب_العمل' => 'required|numeric',
            'الصافي_الربح_للمستثمر' => 'required|numeric',
            'المواد_المستلمة' => 'required',
            'سعر_المواد' => 'required|numeric',
            'إجمالي_المبيعات' => 'required|numeric',
            'صافي_الربح_الكلي' => 'required|numeric',
            'مبلغ_الصيانة' => 'nullable|numeric',
            'مبلغ_الأجور_والمعاملات' => 'nullable|numeric',
            'التوصيات_الرئيسية' => 'required',
            'الخطط_المستقبلية_لتحسين_الأداء' => 'required',
            'project_id' => 'required',

        ]);

        if ($validator->fails()) {
            return $this->apiResponse(null, $validator->errors(), 400);
        }

        $project = Project::find($request->project_id);

        if (!$project) {
            return $this->apiResponse(null, 'Project not found', 404);
        }

        if ($project->user_id != Auth::id()) {
            return $this->apiResponse(null, 'You are not authorized to create a report for this project', 403);
        }

        $report = Report::query()->create([
            'عنوان_التقرير' => $request->عنوان_التقرير,
            'ملخص_الأهداف_المحققة' => $request->ملخص_الأهداف_المحققة,
            'ملخص_الأهداف_غير_المحققة' => $request->ملخص_الأهداف_غير_المحققة,
            'مبلغ_المستثمر' => $request->مبلغ_المستثمر,
            'الإيرادات_الإجمالية' => $request->الإيرادات_الإجمالية,
            'التكاليف_الإجمالية' => $request->التكاليف_الإجمالية,
            'الأرباح_الصافية' => $request->الأرباح_الصافية,
            'الصافي_الربح_لصاحب_العمل' => $request->الصافي_الربح_لصاحب_العمل,
            'الصافي_الربح_للمستثمر' => $request->الصافي_الربح_للمستثمر,
            'المواد_المستلمة' => $request->المواد_المستلمة,
            'سعر_المواد' => $request->سعر_المواد,
            'إجمالي_المبيعات' => $request->إجمالي_المبيعات,
            'صافي_الربح_الكلي' => $request->صافي_الربح_الكلي,
            'مبلغ_الصيانة' => $request->مبلغ_الصيانة,
            'مبلغ_الأجور_والمعاملات' => $request->مبلغ_الأجور_والمعاملات,
            'التوصيات_الرئيسية' => $request->التوصيات_الرئيسية,
            'الخطط_المستقبلية_لتحسين_الأداء' => $request->الخطط_المستقبلية_لتحسين_الأداء,
            'project_id' => $request->project_id,
            'user_id' => Auth::id(),
        ]);

        if ($report) {
            return $this->apiResponse(new ReportResource($report), 'The report was saved successfully', 201);
        }

        return $this->apiResponse(null, 'Failed to save the report', 400);
    }

    public function show( $id)
    {
        $Report= Project::find($id);
        if($Report){
            return $this->apiResponse(new ReportResource($Report) , 'ok' ,200);
        }
        return $this->apiResponse(null ,'the Report not found' ,404);
    }

//
//    public function update(Request $request, $id)
//    {
//        $report = Report::find($id);
//
//        if (!$report) {
//            return $this->apiResponse(null, 'The report was not found', 404);
//        }
//
//        $project = $report->project;
//
//        if (!$project) {
//            return $this->apiResponse(null, 'Project not found', 404);
//        }
//
//        if ($project->user_id != Auth::id()) {
//            return $this->apiResponse(null, 'You are not authorized to update this report', 403);
//        }
//
//        $report->update($request->all());
//
//        return $this->apiResponse(new ReportResource($report), 'The report was updated successfully', 200);
//    }
//
//    public function destroy($id)
//    {
//        $report = Report::find($id);
//
//        if (!$report) {
//            return $this->apiResponse(null, 'The report was not found', 404);
//        }
//
//        $project = $report->project;
//
//        if (!$project) {
//            return $this->apiResponse(null, 'Project not found', 404);
//        }
//
//        if ($project->user_id != Auth::id()) {
//            return $this->apiResponse(null, 'You are not authorized to delete this report', 403);
//        }
//
//        $report->delete();
//
//        return $this->apiResponse(null, 'The report was deleted successfully', 200);
//    }

}

