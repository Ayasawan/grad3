<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Interest extends Model
{
    use HasFactory;
    protected $table = "interests";

    protected $fillable = ['name',];

    protected $primaryKey = "id";
    public $timestamps = true ;
    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_interest', 'interest_id', 'project_id');
    }
    public function investors()
    {
        return $this->belongsToMany(Investor::class, 'investor_interest', 'interest_id', 'investor_id');
    }


}
