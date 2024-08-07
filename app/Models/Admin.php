<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class Admin extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password' ,'user_type','bank_account_number',
    ];

    protected $hidden = [
        'password',
        ];


        public function sentMessages()
        {
            return $this->morphMany(Message::class, 'sender');
        }
    
        public function receivedMessages()
        {
            return $this->morphMany(Message::class, 'receiver');
        }
}
