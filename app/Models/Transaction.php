<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $table = "transactions";
    protected $fillable = [
        'name',
        'price',
        'discount',
        'project_id',
        'user_id',
        'description',
        'status'
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
