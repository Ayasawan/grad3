<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $table = "reports";
    protected $fillable = [
            'عنوان_التقرير',
            'ملخص_الأهداف_المحققة',
            'ملخص_الأهداف_غير_المحققة',
            'مبلغ_المستثمر',
            'الإيرادات_الإجمالية',
            'التكاليف_الإجمالية',
            'الأرباح_الصافية',
            'الصافي_الربح_لصاحب_العمل',
            'الصافي_الربح_للمستثمر',
            'المواد_المستلمة',
            'سعر_المواد',
            'إجمالي_المبيعات',
            'صافي_الربح_الكلي',
            'مبلغ_الصيانة',
            'مبلغ_الأجور_والمعاملات',
            'التوصيات_الرئيسية',
            'الخطط_المستقبلية_لتحسين_الأداء',
            'project_id',
            'user_id',
        ];


    protected $primaryKey = "id";
    public $timestamps = true ;

    public function project(){
        return $this->belongsTo(Project::class,'project_id');
    }


    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }
}
