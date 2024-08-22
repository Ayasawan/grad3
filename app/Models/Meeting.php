<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{
    use HasFactory;
    protected $table = "meetings";

    protected $fillable = [
        'status_meeting', 'investor_id' , 'user_id' , 'appointment_id' , 'project_id', 'meeting_date'];

    
    protected $primaryKey = "id";

    public $timestamps=true ;

    public function investor(){
        return $this->belongsTo(Investor::class,'investor_id');
    }

    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }

    public function project(){
        return $this->belongsTo(Project::class,'project_id');
    }
}
