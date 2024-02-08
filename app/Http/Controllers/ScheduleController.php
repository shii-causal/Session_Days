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
    public function create(Request $request, Schedule $Schedule){
        
        //バリデーション
        $request->validate([
            'title' => 'required',
            'start-date' => 'required',
            'end-date' => 'required',
        ]);
        
        //データベースへの登録
        $schedule->title = $request->input('title');
        $schedule->body = $request->input('body');
        $schedule->start_date = $request->input('start_date');
        $schedule->end_date = date("Y-m-d", strtotime("{$request->input('end_date')} +1 day")); //Full Calendarの仕様でずれる値を修正
        $schadule->start_time = $request->input('start_time');
        $schadule->end_time = $request->input('end_time');
        $schadule->save();
        
        //カレンダー表示画面にリダイレクトする
        return redirect(route("show"));
    }
}
