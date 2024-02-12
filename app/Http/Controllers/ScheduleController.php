<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Schedule;

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
        $schedule->start_time = $request['start_date']." ".$request['start_time'];
        $schedule->end_time = date("Y-m-d", strtotime("{$request['end_date']} +1 day"))." ".$request['end_time'];
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
            'start_time' => 'nullable|integer',
            'end_time' => 'nullable|integer',
        ]);
        
        // 現在カレンダーが表示している日付の期間
        $start_date = date('Y-m-d', $request->input('start_date') / 1000); // 日付変換（JSのタイムスタンプはミリ秒なので秒に変換）
        $end_date = date('Y-m-d', $request->input('end_date') / 1000);
        
        // 予定取得処理（これがaxiosのresponse.dataに入る）
        return $schedule->query()
        
            // DBから取得する際にFullCalendarの形式にカラム名を変更する
            ->select(
                'title',
                'body as description',
                'start_time as start',
                'end_time as end',
            )
            // 表示されているカレンダーのscheduleのみをDBから検索して表示
            ->where('end_date', '>', $start_date)
            ->where('start_date', '<', $end_date)
            ->get();
    }
}
