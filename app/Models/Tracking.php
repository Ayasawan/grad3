<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tracking extends Model
{
    use HasFactory;

    
    protected $table = "trackings";

    protected $fillable = ['earning','cost','tax','outcome','resources_change','project_id',];

    protected $primaryKey = "id";
    public $timestamps = true ;



    public function project(){
        return $this->belongsTo(User::class,'project_id');
    }



}
