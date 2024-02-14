<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;

class EventController extends Controller
{
    //イベント作成画面表示
    public function newEvent(){
        return view("events.create_event");
    }
    
    public function createEvent(Request $request, Event $event){
        
        //バリデーション
        $request->validate([
            'title' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'deadline_date' => 'required'
        ]);
        
        //データベースへの登録
        $event->user_id = $request['user_id'];
        $event->title = $request['title'];
        $event->body = $request['body'];
        $event->start_date = $request['start_date']; //終日の予定
        $event->end_date = $request['end_date'];
        $event->deadline = $request['deadline_date']." ".$request['deadline_time'];
        $event->save();
        
        //URLシェア画面にリダイレクトする
        return redirect(route("newEvent"));
    }
}
