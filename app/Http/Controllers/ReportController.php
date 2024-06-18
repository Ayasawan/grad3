<?php

namespace App\Http\Controllers;
use App\Models\Investor;
use App\Models\Project;
use App\Models\Report;
use App\Http\Controllers\Controller;
use App\Models\User;
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
        $report =  ReportResource::collection(Report::get());
        return $this->apiResponse($report, 'ok', 200);

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

    public function store(Request $request, $projectId)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(null, $validator->errors(), 400);
        }

        $project = Project::find($projectId);

        if (!$project) {
            return $this->apiResponse(null, 'Project not found', 404);
        }

        if ($project->user_id != Auth::id()) {
            return $this->apiResponse(null, 'You are not authorized to create a report for this project', 403);
        }

        $report = Report::query()->create([
            'report_title' => $request->report_title,
            'achieved_goals_summary' => $request->achieved_goals_summary,
            'unachieved_goals_summary' => $request->unachieved_goals_summary,
            'investor_amount' => $request->investor_amount,
            'total_revenue' => $request->total_revenue,
            'total_costs' => $request->total_costs,
            'net_profit' => $request->net_profit,
            'net_profit_employer' => $request->net_profit_employer,
            'net_profit_investor' => $request->net_profit_investor,
            'received_materials' => $request->received_materials,
            'material_price' => $request->material_price,
            'total_sales' => $request->total_sales,
            'overall_net_profit' => $request->overall_net_profit,
            'maintenance_amount' => $request->maintenance_amount,
            'wages_and_transactions_amount' => $request->wages_and_transactions_amount,
            'main_recommendations' => $request->main_recommendations,
            'project_id' => $projectId,
            'user_id' => Auth::id(),
        ]);

        if ($report) {
            return $this->apiResponse(new ReportResource($report), 'The report was saved successfully', 201);
        }

        return $this->apiResponse(null, 'Failed to save the report', 400);
    }

//admin
    public function show( $id)
    {
        $Report= Project::find($id);
        if($Report){
            return $this->apiResponse(new ReportResource($Report) , 'ok' ,200);
        }
        return $this->apiResponse(null ,'the Report not found' ,404);
    }
//مستثمر
    public function showReportsFor_investor(Request $request, $project_id)
    {
        $investor = auth()->user();

        if (!$investor) {
            return $this->apiResponse(null, 'User not authenticated', 401);
        }

        // الحصول على المشروع المطلوب
        $project = Project::with('reports')->find($project_id);

        if (!$project) {
            return $this->apiResponse(null, 'Project not found', 404);
        }

        // التحقق مما إذا كان المستثمر يملك المشروع
        if ($project->investor_id !== $investor->id) {
            return $this->apiResponse(null, 'This project does not belong to the investor', 403);
        }

        // الحصول على التقارير المرتبطة بالمشروع
        $reports = $project->reports;

        return $this->apiResponse($reports, 'OK', 200);
    }

    public function showReportsFor_user(Request $request, $project_id)
    {
        $user = auth()->user();

        if (!$user) {
            return $this->apiResponse(null, 'User not authenticated', 401);
        }

        // الحصول على المشروع المطلوب
        $project = Project::with('reports')->find($project_id);

        if (!$project) {
            return $this->apiResponse(null, 'Project not found', 404);
        }

        // التحقق مما إذا كان المستثمر يملك المشروع
        if ($project->user_id !== $user->id) {
            return $this->apiResponse(null, 'This project does not belong to the user', 403);
        }

        // الحصول على التقارير المرتبطة بالمشروع
        $reports = $project->reports;

        return $this->apiResponse($reports, 'OK', 200);
    }


    public function showReports($user_id)
    {
        $user = User::find($user_id);

        if (!$user) {
            return $this->apiResponse(null, 'User not found', 404);
        }

        $projects = $user->projects()->with('reports')->get();

        if ($projects->isEmpty()) {
            return $this->apiResponse(null, 'No projects found for the user', 404);
        }

        $userReports = [];

        foreach ($projects as $project) {
            $userReports[] = [
                'project' => $project,
                'reports' => ReportResource::collection($project->reports),
            ];
        }

        return $this->apiResponse($userReports, 'OK', 200);
    }


    public function specificProjectReport($project_id, $report_id)
    {
        $user = auth()->user();

        if (!$user) {
            return $this->apiResponse(null, 'User not authenticated', 401);
        }

        $project = Project::where('id', $project_id)
            ->where(function($query) use($user) {
                $query->where('user_id', $user->id)
                    ->orWhere('investor_id', $user->id);
            })
            ->first();

        if (!$project) {
            return $this->apiResponse(null, 'Project not found', 404);
        }

        $report = $project->reports()->find($report_id);

        if (!$report) {
            return $this->apiResponse(null, 'Report not found for the project', 404);
        }

        return $this->apiResponse(new ReportResource($report), 'OK', 200);
    }



}
