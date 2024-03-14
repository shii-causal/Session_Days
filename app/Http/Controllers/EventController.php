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
        
        // event_usersテーブルに登録
        $event->users()->attach($request['user_id']);
        
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
        return view("events.create_event")->with([
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
    
    // 参加希望登録
    public function attendEvent(Request $request, Event $event){
        $input_event = $request['event'];
        $input_user = $request['user'];
        
        // event_usersテーブルに登録
        $event->users()->attach($input_user);
        
        // 参加希望画面に戻る
        return back();
    }
    
    // 日程調整画面の表示
    public function adjust(Event $event){
        
        $return = "";
        
        return view("events.adjust_event")->with([
            'event' => $event,
            'return' => $return
        ]);
    }
    
    // イベント参加者の日程抽出
    public function adjustSchedule(Request $request, Event $event){
        
        // get()では複数と判断されるので、first()を用いる
        $event = $event->query()->where('id', '=', "{$request['event_id']}")->first();
        
        $start_time = "{$request['start_hour']}:{$request['start_minute']}:00";
        $end_time = "{$request['end_hour']}:{$request['end_minute']}:00";
        $time = $request['time'];
        
        $adjust_start_day = $event->start_date;
        $adjust_end_day = $event->end_date;
        
        // 予定がない->0 予定がある=>1 として30分ごとの予定をBitで表現する
        // 例：10::00~18:00 / 00 11 10 00 00 00 11 11 / 11:00~12:30, 16:00~18:00 に予定あり
        
        // イベント参加者それぞれの予定をbitに変換
        $user_bit_schedule_list = [];
        foreach ($event->users as $user)
        {
            // 予定を日ごとに分解する
            $busy_days_list = [];
            foreach ($user->schedules()
                    ->where('end_date', '>=', $adjust_start_day)
                    ->where('start_date', '<=', $adjust_end_day)->get() as $schedule)
            {
                $busy_days_list[] = Event::scheduleToDay($adjust_start_day, $adjust_end_day, $schedule);
            }
            
            // 日ごとに分解した予定をBitに変換する
            $bit_schedule_list = [];
            foreach ($busy_days_list as $busy_days)
            {
                
                foreach ($busy_days as $busy_day)
                {
                    $busy_day_list = "";
                    $busy_day_list = Event::scheduleToBit($start_time, $end_time, $busy_day);
                    // 同日の予定をまとめて配列に入れる
                    $bit_schedule_list = array_merge_recursive($busy_day_list, $bit_schedule_list);
                }
            }
            
            // 同日の予定をまとめ（論理和）配列に入れ直す
            foreach ($bit_schedule_list as $day=>$bit)
            {
                if (is_countable($bit)) {
                    
                    // 同日の予定が複数あるとき一つにまとめる
                    for ($i=1; $i<count($bit); $i++)
                    {
                        $bit[0] = Event::takeLogicalSum($bit[$i],$bit[0]);
                    }
                    
                    // 予定をまとめたbit以外を削除して再セット
                    $bit_schedule_list[$day] = $bit[0];
                }
            }
            
            $user_bit_schedule_list[$user->id] = $bit_schedule_list;
        }
        
        // イベント参加者全員の予定をまとめる
        foreach ($user_bit_schedule_list as $user=>$bit_schedule_list)
        {
            foreach ($bit_schedule_list as $day=>$bit)
            {
                $bit_list[$day][] = $bit;
            }
        }
        
        // 参加者全員の予定を論理和でまとめる
        foreach ($bit_list as $day=>$bit)
        {
            if (count($bit) > 1) {
                    
                    // 同日の予定が複数あるとき一つにまとめる
                    for ($i=1; $i<count($bit); $i++)
                    {
                        $bit[0] = Event::takeLogicalSum($bit[$i],$bit[0]);
                    }
                    
                    // 予定をまとめたbit以外を削除して再セット
                    $bit_schedule_list[$day] = $bit[0];
            } else {
                
                $bit_schedule_list[$day] = $bit[0];
            }
        }
        
        // 予定がある日の空き時間をまとめる
        $free_time_list = Event::getFreeTime($start_time, $end_time, $bit_schedule_list);
        
        // 予定のない日を補完
        
        // 日数差を計算
        $start_day = new Carbon($adjust_start_day);
        $end_day = new Carbon($adjust_end_day);
        $day_number = $start_day->diffInDays($end_day);
        
        $day_start = Carbon::createFromTimeString($start_time);
        $day_end = Carbon::createFromTimeString($end_time);
        
        // 予定のない日を補完
        $days = [];
        for ($i=0; $i<=$day_number; $i++)
        {
            $day = $start_day->addDays($i)->format('Y-m-d');
            
            // 1日毎に配列に存在するかを判断して補完
            if (! array_key_exists($day, $free_time_list)) {
                
                $free_time_list[$day][] =
                    "{$day_start->format('H:i')}~{$day_end->format('H:i')}";
            }

            $start_day = new Carbon($adjust_start_day);
        }
        
        // 指定の時間以上空いている日を抽出
        $free_date_list = [];
        foreach ($free_time_list as $day=>$free_times)
        {
            $free_list = [];
            foreach ($free_times as $free_time)
            {
                // 空いている時間を計算
                $free_start_time = new Carbon(substr($free_time, 0, 5));
                $free_end_time = new Carbon(substr($free_time, 6, 5));
                $period = $free_start_time->diffInHours($free_end_time);
                
                // 指定の時間以上空いている時間をセット
                if ($period >= $time) {
                    
                    $free_list[] = $free_time;
                }
            }
            
            // 日付をキーにしてセット
            foreach ($free_list as $free)
            {
                $free_date_list[$day][] = $free;
            }
        }
        
        // 日付順に並び替え
        ksort($free_date_list);
        
        $return = $user_bit_schedule_list[1];
        
        // 全員の空き時間が合わない場合
        if ($free_date_list == NULL) {
            
            $return = "全員の予定が合う日時はありませんでした";
        }
        
        return view('events.adjust_event')->with([
            'event' => $event,
            'return' => $return
        ]);
    }
}
