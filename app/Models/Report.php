<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $table = "reports";
    protected $fillable = [
        'report_title',
        'achieved_goals_summary',
        'unachieved_goals_summary',
        'investor_amount',
        'total_revenue',
        'total_costs',
        'net_profit',
        'net_profit_employer',
        'net_profit_investor',
        'received_materials',
        'material_price',
        'total_sales',
        'overall_net_profit',
        'maintenance_amount',
        'wages_and_transactions_amount',
        'main_recommendations',
        'project_id',
        'user_id',
    ];

    protected $primaryKey = "id";
    public $timestamps = true;

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
