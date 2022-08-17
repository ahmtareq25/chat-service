<?php

namespace App\Http\Resources;


use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class ChatRoomDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */

    //  this resource is used to push/trigger event to another user with authenticated user details
    public function toArray($request)
    {
        $user=Auth::user();
        return [
            'id'=>$this->id,
            'user_id'=>$user->id,
            'name'=>$user->name,
            'avatar'=>asset("profile/$user->profile_image"),
            'unread'=>$this->unread,
            'message'=>$this->lastMessage->last_message,
            'time'=>$this->lastMessage->created_at
        ];
    }
}
