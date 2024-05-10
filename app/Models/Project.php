<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;


    protected $table = "projects";

    protected $fillable = ['description','feasibility_study','amount','location','investor_id','user_id','type_id',];

    protected $primaryKey = "id";
    public $timestamps = true ;


    public function investor(){
        return $this->belongsTo(Investor::class,'investor_id');
    }

    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }

    public function type(){
        return $this->belongsTo(Type::class,'type_id');
    }


    public function trackings(){
        return $this->hasOne( Tracking::class,'project_id');
    }

    public function complaints(){
        return $this->hasMany( Complaint::class,'project_id');
    }

    public function statistics(){
        return $this->hasMany( Statistic::class,'project_id');
    }

    public function interests()
    {
        return $this->belongsToMany(Interest::class, 'project_interest', 'project_id', 'interest_id');
    }
    public function reports(){
        return $this->hasMany( Report::class,'project_id');
    }

    public function transactions(){
        return $this->hasMany( Transaction::class,'project_id');
    }

    public function evaluations(){
        return $this->hasMany( Evaluation::class,'project_id');
    }
}
