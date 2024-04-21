<?php

namespace App\Http\Controllers;
use App\Models\Project;
use App\Models\Investor;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class EvaluationController extends Controller
{
    public function index(Request $request ,$id)
    {      $project = Project::find($id);
        $evaluations = $project->evaluations()->get();
        return response()->json($evaluations);
    }

    // public function store(Request $request ,$id)

    // {
    //     $project = Project::find($id);
    //     if($project->evaluations()->where('user_id',Auth::id())->exists())
    //     {
    //         $project->evaluations()->where('user_id',Auth::id())->delete();
    //     }
    //     else
    //     {
    //         $project->evaluations()->create(['user_id'=>Auth::id()]);
    //     }

    //     return response()->json(null);
    // }

  
    // public function dislike($id)
    // {
    //     $project = Project::find($id);
    //     if ($project) {
    //         $evaluation = $project->likes()->where('user_id', Auth::id())->first();
    //         if ($evaluation) {
    //             $evaluation->delete(); // Remove the existing like (dislike the complaint)
    //         }
    //     }

    //     return response()->json(null);
    // }





//     public function investorStore(Request $request, $id)
// {
//     $project = Project::find($id);
//     $user = Auth::user(); // استخدم Auth::user() للوصول إلى نموذج المستخدم المسجل الحالي
//     if($user->user_type == "investor"){
//     if ($project->evaluations()->where('evaluable_type', Investor::class)->where('evaluable_id', $user->id)->exists()) {
//         $project->evaluations()->where('evaluable_type', Investor::class)->where('evaluable_id', $user->id)->delete();
//     } else {
//         $project->evaluations()->create([
//             'evaluable_type' => Investor::class,
//             'evaluable_id' => $user->id,
//         ]);
//     }}
//     else{
//         if ($project->evaluations()->where('evaluable_type', User::class)->where('evaluable_id', $user->id)->exists()) {
//             $project->evaluations()->where('evaluable_type', User::class)->where('evaluable_id', $user->id)->delete();
//         } else {
//             $project->evaluations()->create([
//                 'evaluable_type' => User::class,
//                 'evaluable_id' => $user->id,
//             ]);
//         }
//     }

//     return response()->json(null);
// }



public function store(Request $request, $id)
{
    $project = Project::find($id);
    $user = Auth::user();

    if ($user->user_type === "investor") {
        $evaluation = $project->evaluations()
            ->where('evaluable_type', Investor::class)
            ->where('evaluable_id', $user->id)
            ->first();

        if ($evaluation) {
            $evaluation->delete();
        } else {
            $project->evaluations()->create([
                'evaluable_type' => Investor::class,
                'evaluable_id' => $user->id,
            ]);
        }
    } else {
        $evaluation = $project->evaluations()
            ->where('evaluable_type', User::class)
            ->where('evaluable_id', $user->id)
            ->first();

        if ($evaluation) {
            $evaluation->delete();
        } else {
            $project->evaluations()->create([
                'evaluable_type' => User::class,
                'evaluable_id' => $user->id,
            ]);
        }
    }

    return response()->json(null);
}



    // public function investorDislike($id)
    // {
    //     $project = Project::find($id);
    //     $user = Auth::user(); // استخدم Auth::user() للوصول إلى نموذج المستخدم المسجل الحالي

    //     if ($project) {
    //         $evaluation = $project->evaluations()
    //             ->where('evaluable_type', Investor::class)
    //             ->where('evaluable_id', $user->id)
    //             ->first();

    //         if ($evaluation) {
    //             $evaluation->delete();
    //         }
    //     }

    //     return response()->json(null);
    // }

    public function destroy($id)
{
    $project = Project::find($id);
    $user = Auth::user();

    if ($project) {
        $evaluation = $project->evaluations()
            ->where(function ($query) use ($user) {
                $query->where('evaluable_type', Investor::class)
                    ->where('evaluable_id', $user->id);
            })
            ->orWhere(function ($query) use ($user) {
                $query->where('evaluable_type', User::class)
                    ->where('evaluable_id', $user->id);
            })
            ->first();

        if ($evaluation) {
            $evaluation->delete();
        }
    }

    return response()->json(null);
}
}


