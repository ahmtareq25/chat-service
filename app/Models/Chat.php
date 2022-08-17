<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Chat extends Model
{
    use HasFactory;

    protected $fillable = ['type'];


    // relations
    public function users(){
        return $this->belongsToMany(User::class)->withTimestamps();
    }
    public function messages(){
        return $this->hasMany(ChatMessage::class);
    }
    public function lastMessage(){
        return $this->hasOne(ChatMessage::class)->latestOfMany();
    }
}
