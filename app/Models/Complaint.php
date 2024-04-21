<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    use HasFactory;

    protected $table = "complaints";

    protected $fillable = ['description','project_id','investor_id',];

    protected $primaryKey = "id";
    public $timestamps = true ;



    public function project(){
        return $this->belongsTo(User::class,'project_id');
    }


    public function investor(){
        return $this->belongsTo(User::class,'investor_id');
    }
}
