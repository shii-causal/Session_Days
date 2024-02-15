<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use Illuminate\Support\Facades\URL;
use Carbon\Carbon;

class EventController extends Controller
{
    //イベント作成画面表示
    public function newEvent(){
        //参加希望画面のURL未作成
        $url = 0;
        return view("events.create_event")->with('url', $url);
    }
    
    //DBに新規イベント内容を登録
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
        
        //有効期限の設定
        $now = new Carbon(); //現在の日時を取得
        $deadline = new Carbon($event->deadline); //回答期限をCarbonインスタンスに変換
        $diff = $now->diffInMinutes($deadline); //回答期限までの分数を計算
        
        //参加URL（期限付き）を作成
        $url = URL::temporarySignedRoute(
            "attend",
            now()->addMinutes($diff), //$diffの分後まで有効
            ['event' => $event]
        );
        
        //URLシェア画面にリダイレクトする
        return view("/events/create_event")->with([
            'event' => $event,
            'url' => $url
        ]);
    }
    
    //参加希望画面の表示
    public function attend(Request $request, Event $event){
    
        //URLの有効期限を確認する
        if (! $request->hasValidSignature()) {
            return view("events.expired");
        }
    
        return view("events.attend_event")->with('event', $event);
    }
}
