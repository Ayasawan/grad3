<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
// use Laravel\Sanctum\HasApiTokens;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;


    protected $table = "users";

    protected $fillable = ['first_name','last_name','user_type','email','otp','device_token','password','phone','location','iD_card','personal_photo','property_deed','clean_record'];

    protected $primaryKey = "id";
    public $timestamps = true ;
    protected $hidden = [
        'password',
        'remember_token',
    ];


    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function reports(){
        return $this->hasMany( Report::class,'user_id');
    }

    public function transactions(){
        return $this->hasMany(Transaction::class,'user_id');
    }

    public function evaluations()
    {
        return $this->morphMany(Evaluation::class, 'evaluable');
    }

    public function notifications()
    {
        return $this->morphMany(Notification::class, 'notifiable');
    }

    public function projects(){
        return $this->hasMany(Project::class,'user_id');
    }

    public function sentMessages()
    {
        return $this->morphMany(Message::class, 'sender');
    }

    public function receivedMessages()
    {
        return $this->morphMany(Message::class, 'receiver');
    }
}
