<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evaluation extends Model
{
    use HasFactory;

  
    protected $table = "evaluations";

    protected $fillable = [
        'evaluable_id',
        'evaluable_type',
        'project_id',
    ];

    
    protected $primaryKey = "id";

    public $timestamps=true ;



    public function evaluable()
    {
        return $this->morphTo();
    }

    public function project(){
        return $this->belongsTo(Project::class,'project_id');
    }
}
