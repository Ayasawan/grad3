<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;
    protected $table = "appointments";

    protected $fillable = [
        'hour', 'status_hour' , 'time_period','date'];

    
    protected $primaryKey = "id";

    public $timestamps=true ;

}
