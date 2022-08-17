<?php

namespace App\Services;

use App\Events\NotificationEvent;
use App\Http\Resources\NotificationUserResource;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\Chat;
use App\Models\ChatMessage;
use App\Http\Events\MessageReactionEvent;
use App\Events\MessageSentEvent;
use App\Traits\ApiResponse;
use App\Http\Resources\ChatMessageResource;
use App\Http\Resources\ChatRoomResource;
use App\Events\RoomMessageSentEvent;
use Symfony\Component\HttpFoundation\Response;

class ChatService{

    use ApiResponse;

    public function rooms(){
        DB::enableQueryLog();
        $user=Auth::user();
        $search=request('search','');
        $rooms=$user->chats()->withCount(['messages as unread'=>function(Builder $query) use($user){
                    $query->where('sender_id','!=',$user->id)->where(function($qry) use($user){
                        $qry->whereNull('read_by')->orWhereJsonDoesntContain('read_by',$user->id);
                    });
                }])->with(['lastMessage','users'=>function($query) use($user){
                    $query->where('users.id','!=',$user->id);
                }])->whereHas('messages',function($qry) use($user){
                    $qry->whereNull('deleted_for')->orWhereJsonDoesntContain('deleted_for',$user->id);
                })->orderByDesc(
                    ChatMessage::select('created_at')
                        ->whereColumn('chat_id','chats.id')
                        ->orderByDesc('created_at')
                        ->limit(1)
                )->when($search!='',function($query) use($search,$user){
                    return $query->whereHas('users',function($u) use($search,$user){
                        $u->where('name','like',"%$search%")->where('user_id','!=',$user->id);
                    });
                })->get();

        Log::info(DB::getQueryLog());
        return $this->successResponse(new ChatRoomResource($rooms));
    }

    // get room messages by user id
    public function messages($user_id){
        $user=Auth::user();
        $room=$user->chats()->whereHas('users',function(Builder $qry) use($user_id){
            $qry->where('users.id',$user_id);
        })->first();
        if(!$room){
            $room=Chat::create(['type'=>'single']);
            $room->users()->attach([$user->id,$user_id]);
        }
        $message_id=request('message_id',0);
        if($message_id==0){
            $this->markMessagesAsRead($room);
        }
        $messages=$room->messages()->with('room')->where(function($qry) use($user){
                    $qry->whereNull('deleted_for')->orWhereJsonDoesntContain('deleted_for',$user->id);
                })->orderByDesc('id')->when($message_id>0,function($query) use($message_id){
                    return $query->where('id','<',$message_id);
                })->take(20)->get();
        $second_user=User::findOrFail($user_id);
        $data=[
            'messages'=>ChatMessageResource::collection($messages),
            'user'=>$second_user->name,
            'avatar'=>asset($second_user->avatar),
            'room_id'=>$room->id,
            'my_id'=>$user->id,
        ];
        return $this->successResponse($data);
    }

    public function sendMessage(Request $request,$user_id){
        $user=Auth::user();
        $room=$user->chats()->whereHas('users',function(Builder $qry) use($user_id){
            $qry->where('users.id',$user_id);
        })->first();
        if(!$room){
            $room=Chat::create(['type'=>'single']);
            $room->users()->attach([$user->id,$user_id]);
        }
         $message=$room->messages()->create([
             'sender_id'=>$user->id,
             'message'=>$request->message??''
         ]);
        if($file=$request->file('attachment')){
            $attachment=new ChatAttachmentService($request->attachment_name,$message->id);
            $attachment->upload($file,$request->attachment_type,$message->id);
        }
        $second_user=User::findOrFail($user_id);
        $data=new ChatMessageResource($message);
        $this->broadcastMessage($room->id,$data,$second_user,$message);
        return $this->successResponse($data,'message sent successfully');
    }

    public function markMessagesAsRead($room){
        $user=Auth::user();
        $messages=$room->messages()->where('sender_id','!=',$user->id)->where(function($qry) use($user){
            $qry->whereNull('read_by')->orWhereJsonDoesntContain('read_by',$user->id);
        })->select('id')->get();
        $messages_id=$messages->pluck('id')->toArray();
        if(count($messages_id)>0){
            $messages_id=join(",",$messages_id);
            DB::update("update chat_messages set read_by=case when read_by is null then '[$user->id]'  else JSON_ARRAY_APPEND(read_by,'$',$user->id) end where id in ($messages_id)");
        }
    }

    // broadcast notifications
    public function broadcastMessage($room_id,$data,$second_user,$message){
        $user_id=Auth::id();
        broadcast(new MessageSentEvent($user_id,$room_id,$data));
        $user=Auth::user();
        $room=[
                'id'=>$room_id,
                'user_id'=>$user->id,
                'name'=>$user->name,
                'avatar'=>asset($user->avatar),
                'message'=>$message->last_message,
                'time'=>Carbon::now()
            ];
        broadcast(new RoomMessageSentEvent($second_user->id,$room));
        return response()->json('success');
    }

    public function deleteConversation($id){
        $room=Chat::findOrFail($id);
        $user=Auth::user();
        $messages=$room->messages()->whereNull('deleted_for')->orWhereJsonDoesntContain('deleted_for',$user->id)->select('id')->get();
        $messages_id=$messages->pluck('id')->toArray();
        if(count($messages_id)>0){
            $messages_id=join(",",$messages_id);
            DB::update("update chat_messages set deleted_for=case when deleted_for is null then '[$user->id]'  else JSON_ARRAY_APPEND(deleted_for,'$',$user->id) end where id in ($messages_id)");
        }
        $this->markMessagesAsRead($room);
        return $this->successResponse(null,'deleted successfully');
    }

    public function allUsers(){
        $search=request('search','');
        $users=User::where('id','!=',Auth::id())->select('id','name')
            ->when($search!='',function($qry) use($search){
                $qry->where('name','like',"$search%");
            })
            ->take(50)
            ->get()
            ->toArray();
        return $this->successResponse($users,'all users');
    }

}
