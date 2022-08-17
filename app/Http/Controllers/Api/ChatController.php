<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Services\ChatService;
use App\Traits\ApiResponse;

class ChatController extends Controller
{
    // use ApiResponse;
    private $service;

    public function __construct(ChatService $service)
    {
        $this->service=$service;
    }

    public function rooms(){
        return $this->service->rooms();
    }

    public function messages($user_id){
        return $this->service->messages($user_id);
    }

    public function sendMessage(Request $request,$user_id){
        return $this->service->sendMessage($request,$user_id);
    }

    public function deleteConversation($id){
        return $this->service->deleteConversation($id);
    }
}
