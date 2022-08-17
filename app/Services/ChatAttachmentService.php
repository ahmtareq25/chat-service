<?php

namespace App\Services;


use App\Models\ChatAttachment;

class ChatAttachmentService{

    private $attachment_name;
    private $message_id;

    public function __construct($attachment_name,$message_id)
    {
        $this->attachment_name=$attachment_name;
        $this->message_id=$message_id;
    }

    public function upload($file,$type){
        if($type=='image'){
            return $this->uploadImage($file);
        }elseif($type=='video'){
            return $this->uploadVideo($file);
        }else{
            return $this->uploadDocument($file);
        }
    }

    public function uploadDocument($document){
        $name=time().str_replace(' ','',$document->getClientOriginalName());
        $document->move('chat-attachment/documents', $name);
        $file_name='chat-attachment/documents/'.$name;
        return $this->addAttachment($file_name,'document');
    }


    public function uploadVideo($video){
        $name=time().str_replace(' ','',$video->getClientOriginalName());
        $video->move('chat-attachment/videos', $name);
        $name='chat-attachment/videos/'.$name;
        return $this->addAttachment($name,'video');
    }


    public function uploadImage($image){
        $name=time().str_replace(' ','',$image->getClientOriginalName());
        $image->move('chat-attachment/images', $name);
        $name='chat-attachment/images/'.$name;
        return $this->addAttachment($name,'image');
    }

    public function addAttachment($file,$type){
        ChatAttachment::create([
            'file'=>$file,
            'name'=>$this->attachment_name,
            'type'=>$type,
            'message_id'=>$this->message_id
        ]);
        return response()->json('success');
    }

}
