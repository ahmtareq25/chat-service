<?php

namespace App\Http\Resources;


use Illuminate\Http\Resources\Json\ResourceCollection;

class ChatRoomResource extends ResourceCollection
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $rooms = [];
        foreach ($this->collection as $collection){
            if (!empty($collection)){
                $user=$collection->users[0];
                $rooms[]= [
                    'id'=>$collection->id,
                    'user_id'=>$user->id,
                    'name'=>$user->name,
                    'avatar'=>asset($user->avatar),
                    'unread'=>$collection->unread,
                    'message'=>$collection->lastMessage->last_message,
                    'time'=>$collection->lastMessage->created_at
                ];
            }
        }
        return $rooms;
    }
}
