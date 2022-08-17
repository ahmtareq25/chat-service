<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ChatAttachment extends Model
{
    use HasFactory;

    protected $fillable = ['message_id','type','name','file'];


}
