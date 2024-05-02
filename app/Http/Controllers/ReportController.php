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
    }public function userReports()
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
            'pdf' => 'required',
            'report_date' => 'required',
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

        if ($request->hasFile('pdf')) {
            $file = $request->file('pdf');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('pdf'), $fileName);
        }

        $report = Report::query()->create([
            'pdf' => $fileName,
            'report_date' => $request->report_date,
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

    public function update(Request $request, $id)
    {
        $report = Report::find($id);

        if (!$report) {
            return $this->apiResponse(null, 'The report was not found', 404);
        }

        $project = $report->project;

        if (!$project) {
            return $this->apiResponse(null, 'Project not found', 404);
        }

        if ($project->user_id != Auth::id()) {
            return $this->apiResponse(null, 'You are not authorized to update this report', 403);
        }

        $report->update($request->all());

        return $this->apiResponse(new ReportResource($report), 'The report was updated successfully', 200);
    }

    public function destroy($id)
    {
        $report = Report::find($id);

        if (!$report) {
            return $this->apiResponse(null, 'The report was not found', 404);
        }

        $project = $report->project;

        if (!$project) {
            return $this->apiResponse(null, 'Project not found', 404);
        }

        if ($project->user_id != Auth::id()) {
            return $this->apiResponse(null, 'You are not authorized to delete this report', 403);
        }

        $report->delete();

        return $this->apiResponse(null, 'The report was deleted successfully', 200);
    }

}

