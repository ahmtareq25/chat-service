<?php

namespace App\Http\Controllers;

use App\Services\ChatService;
use Illuminate\Http\Request;

class ChatController extends Controller
{

    private $service;
    public function __construct(ChatService $service)
    {
        $this->service=$service;
    }
    public function index(){
        return view('chat');
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
    public function allUsers(){
        return $this->service->allUsers();
    }
}
