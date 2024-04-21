<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Statistic extends Model
{
    use HasFactory;
    protected $table = "statistics";

    protected $fillable = [
        'earning',
        'financing',
        'type',
        'location',
        'project_id',
    ];

    
    protected $primaryKey = "id";

    public $timestamps=true ;

    public function project(){
        return $this->belongsTo(Project::class,'project_id');
    }
}
