<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $table = "reports";
    protected $fillable = [
        'pdf',
        'report_date',
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
