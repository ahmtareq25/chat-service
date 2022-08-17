<?php

namespace App\Http\Resources;


use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class ChatMessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $auth_id=Auth::id();
        return [
            'id'=>$this->id,
            'send_by_me'=>$this->sender_id==$auth_id?true:false,
            'message'=>$this->message,
            'room_id'=>$this->chat_id,
            'sender_id'=>$this->sender_id,
            'attachment'=>$this->attachment?new ChatAttachmentResource($this->attachment):null,
            'created_at'=>$this->created_at,
        ];
    }
}
