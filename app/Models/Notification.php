<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

  
    protected $table = "notifications";

    protected $fillable = [
        'notifiable_id',
        'notifiable_type',
        'title',
        'body',
    ];

    
    protected $primaryKey = "id";

    public $timestamps=true ;



    public function notifiable()
    {
        return $this->morphTo();
    }
}
