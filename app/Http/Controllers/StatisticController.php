<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\User;
use App\Models\Investor;
use App\Models\Project;
use App\Models\Report;
use Carbon\Carbon;


class StatisticController extends Controller
{
   
//investors_users_accounts
    public function getMonthlyStatistics1()
    {
        $cacheKey = 'monthly_statistics_'.now()->format('Y-m-H'); // create a unique cache key for the current month and hour
    
        if (Cache::has($cacheKey)) {
            // cache key exists, return the cached value
            $statistics = Cache::get($cacheKey);
        } else {
            // cache key doesn't exist, calculate the statistics and cache the result
            $statistics = $this->calculateStatistics();
            Cache::put($cacheKey, $statistics, now()->addHour(1)); // cache for 1 hour
        }
    
        return response()->json($statistics);
    }

    private function calculateStatistics()
    {
        $monthsToShow = 4; 
        $statistics = [];

        for ($i = 0; $i < $monthsToShow; $i++) {
            $currentMonth = now()->subMonths($i)->format('Y-m');

            // حساب العدد الكلي للمستخدمين حتى نهاية الشهر المحدد
            $totalUsers = User::whereDate('created_at', '<=', Carbon::parse($currentMonth)->endOfMonth())->count();
            // حساب العدد الكلي للمستثمرين حتى نهاية الشهر المحدد
            $totalInvestors = Investor::whereDate('created_at', '<=', Carbon::parse($currentMonth)->endOfMonth())->count();

            // إضافة الإحصائيات للمصفوفة
            $statistics[] = [
                'month' => $currentMonth,
                'users' => $totalUsers,
                'investors' => $totalInvestors,
            ];
        }

        return $statistics;
    }


    //projects
    public function getMonthlyProjectStatistics()
    {
        $cacheKey = 'monthly_project_statistics_' . now()->format('Y-m-H'); 
    
        if (Cache::has($cacheKey)) {
            $statistics = Cache::get($cacheKey);
        } else {
            $statistics = $this->calculateProjectStatistics();
            Cache::put($cacheKey, $statistics, now()->addHour(1)); // cache for 1 hour
        }
    
        return response()->json($statistics);
    }

    private function calculateProjectStatistics()
    {
        $monthsToShow = 4; 
        $statistics = [];

        for ($i = 0; $i < $monthsToShow; $i++) {
            $currentMonth = now()->subMonths($i)->format('Y-m');

            $totalProjects = Project::whereDate('created_at', '<=', Carbon::parse($currentMonth)->endOfMonth())->count();
            $fundedProjects = Project::where('investment_status', '1')->whereDate('created_at', '<=', Carbon::parse($currentMonth)->endOfMonth())->count();

            $statistics[] = [
                'month' => $currentMonth,
                'total_projects' => $totalProjects,
                'funded_projects' => $fundedProjects,
            ];
        }

        return $statistics;
    }


    ///الارباح
    public function getMonthlyReportStatistics()
    {
        $cacheKey = 'monthly_project_statistics_' . now()->format('Y-m-d-H'); 

        if (Cache::has($cacheKey)) {
            $statistics = Cache::get($cacheKey);
        } else {
            $statistics = $this->calculateReportStatistics();
            Cache::put($cacheKey, $statistics, now()->addHour(1)); // cache for 1 hour
        }

        return response()->json($statistics);
    }

    private function calculateReportStatistics()
{
    $monthsToShow = 4; 
    $statistics = [];

    for ($i = 0; $i < $monthsToShow; $i++) {
        $currentMonth = now()->subMonths($i);
        $monthName = $currentMonth->format('Y-m');

        $totalNetProfit = 0;
        $totalNetProfitEmployer = 0;
        $totalNetProfitInvestor = 0;
        $totalRevenue = 0;

        $projects = Project::all();

        foreach ($projects as $project) {
            $reports = $project->reports()
                            ->whereYear('created_at', $currentMonth->year)
                            ->whereMonth('created_at', $currentMonth->month)
                            ->get();

            foreach ($reports as $report) {
                $totalNetProfit += $report->net_profit;
                $totalNetProfitEmployer += $report->net_profit_employer;
                $totalNetProfitInvestor += $report->net_profit_investor;
                $totalRevenue += $report->total_revenue;
            }
        }

        $statistics[] = [
            'month' => $monthName,
            'total_net_profit' => $totalNetProfit,
            'total_net_profit_employer' => $totalNetProfitEmployer,
            'total_net_profit_investor' => $totalNetProfitInvestor,
            'total_revenue' => $totalRevenue,
        ];
    }

    return $statistics;
}


}

