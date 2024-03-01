<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Schedule;
use Illuminate\Support\Facades\Auth;

class ScheduleController extends Controller
{
    //カレンダー表示
    public function show(){
        return view("calendars.calendar");
    }
    
    //予定追加
    public function create(Request $request, Schedule $schedule){
        
        //バリデーション
        $request->validate([
            'title' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
        ]);
        
        //データベースへの登録
        $schedule->user_id = $request['user_id'];
        $schedule->title = $request['title'];
        $schedule->body = $request['body'];
        $schedule->start_date = $request['start_date']; //終日の予定
        $schedule->end_date = date("Y-m-d", strtotime("{$request['end_date']} +1 day")); //Full Calendarの仕様でずれる値を修正
        $schedule->start_time = $request['start_time'];
        $schedule->end_time = $request['end_time'];
        $schedule->save();
        
        //カレンダー表示画面にリダイレクトする
        return redirect(route("show"));
    }
    
    //DBからScheduleを取り出し、カレンダーに表示
    public function get(Request $request, Schedule $schedule){
        
        // バリデーション
        $request->validate([
            'start_date' => 'required|integer',
            'end_date' => 'required|integer',
        ]);
        
        // 現在カレンダーが表示している日付の期間
        $start_date = date('Y-m-d', $request->input('start_date') / 1000); // 日付変換（JSのタイムスタンプはミリ秒なので秒に変換）
        $end_date = date('Y-m-d', $request->input('end_date') / 1000);
        
       
            
        // カレンダーが表示している期間の予定を取得
        $scheduleData = $schedule->query()
            ->select(
                    'title',
                    'body',
                    'start_date',
                    'end_date',
                    'start_time',
                    'end_time'
                )
            ->where('end_date', '>', $start_date)
            ->where('start_date', '<', $end_date) // AND条件
            ->where('user_id', '=', Auth::user()->id)
            ->get();
        
        // 予定の配列を再セット
        $scheduleList = [];
        
        // FullCalendarの形式に変更
        foreach ($scheduleData as $event){
            
            if ($event->start_time == NULL) {
            // 終日の予定    
                
                $scheduleList[] = [
                        
                        'title' => $event->title,
                        'description' => $event->body,
                        'start' => $event->start_date,
                        'end' => $event->end_date,
                        'allDay' => true
                        
                    ];
            } else {
            // 時間が決まった予定
                
                $scheduleList[] = [
                        
                        'title' => $event->title,
                        'description' => $event->body,
                        'start' => $event->start_date." ".$event->start_time,
                        'end' => $event->end_date." ".$event->end_time
                        
                    ];
            }
        }
        
        // json形式でJSのresponse.dataに返す
        return json_encode($scheduleList);
    }
}
