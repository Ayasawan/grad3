<?php

namespace App\Services;

use App\Models\User;
use App\Models\Investor;
use Illuminate\Support\Facades\Cache;

class StatisticsService
{

    //not_used
    public function calculateAndCacheMonthlyStatistics()
    {
        $currentMonth = now()->format('Y-m');

        $totalUsers = User::whereDate('created_at', '<=', now()->endOfMonth())->count();

        $totalInvestors = Investor::whereDate('created_at', '<=', now()->endOfMonth())->count();

        Cache::put("statistics.{$currentMonth}.users", $totalUsers, now()->addMonth()->startOfMonth());
        Cache::put("statistics.{$currentMonth}.investors", $totalInvestors, now()->addMonth()->startOfMonth());
    }

     //not_used
    public function getMonthlyStatistics()
    {
        $currentMonth = now()->format('Y-m');

        $totalUsers = Cache::get("statistics.{$currentMonth}.users", 0);
        $totalInvestors = Cache::get("statistics.{$currentMonth}.investors", 0);

        return [
            'month' => $currentMonth,
            'users' => $totalUsers,
            'investors' => $totalInvestors,
        ];
    }
}
