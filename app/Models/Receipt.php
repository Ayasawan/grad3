<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Receipt extends Model
{
    use HasFactory;

    protected $table = "receipts";
    protected $fillable = [

        'image',
        'transaction_id'
    ];

    protected $primaryKey = "id";
    public $timestamps = true ;

    public function Transaction(){
        return $this->belongsTo(Transaction::class,'transaction_id');
    }

}
