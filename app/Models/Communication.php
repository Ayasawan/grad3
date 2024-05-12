<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Communication extends Model
{

    use HasFactory;

    protected $table = "communications";

    protected $fillable = ['project_id', 'investor_id', 'status'];

    protected $primaryKey = "id";
    public $timestamps = true ;

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function investor()
    {
        return $this->belongsTo(Investor::class, 'investor_id');
    }
}