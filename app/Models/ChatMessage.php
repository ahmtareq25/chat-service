<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ChatMessage extends Model
{
    use HasFactory;

    public $with=['attachment'];

    protected $fillable = ['chat_id','sender_id','message','read_by','deleted_for'];



    // accessors and mutators
    public function getLastMessageAttribute(){
        if($this->attachment){
            return $this->message!=''?$this->message:$this->attachment->name;
        }
        return $this->message;
    }
    public function getReadByAttribute($val){
        return $val?json_decode($val):array();
    }
    public function getDeletedForAttribute($val){
        return $val?json_decode($val):array();
    }
    public function getNotificationInfoAttribute(){
        if($this->attachment){
            return "Sent You an ".$this->attachment->type;
        }
        return $this->message;
    }
    public function setReadByAttribute($val){
        $read_by=$this->read_by;
        array_push($read_by,$val);
        $this->attributes['read_by']=json_encode($read_by);
    }
    public function setDeletedForAttribute($val){
        $deleted_for=$this->deleted_for;
        array_push($deleted_for,$val);
        $this->attributes['deleted_for']=json_encode($deleted_for);
    }


    // relations
    public function attachment(){
        return $this->hasOne(ChatAttachment::class,'message_id');
    }
    public function sender(){
        return $this->belongsTo(User::class,'sender_id');
    }
    public function room(){
        return $this->belongsTo(Chat::class,'chat_id');
    }

}
