<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Event extends Model
{
    use HasFactory;
    use SoftDeletes;
    
    protected $fillable = [
        'user_id',
        'title',
        'body',
        'start_date',
        'end_date',
        'deadline'
    ];
    
    // 日付をformat()で整形できるようにする
    protected $dates = ['start_date', 'end_date', 'deadline'];
    
    // Usersに対するリレーション（多対１）：イベント作成者
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    // // Usersに対するリレーション（多対１）：イベント参加者
    public function users()
    {
        return $this->belongsToMany(User::class);
    }
    
    // 予定がある日を分割する関数
    public static function scheduleToDay($adjust_start_day, $adjust_end_day, $schedule)
    {
        // $scheduleを再セット
        $event_start = $schedule->start_time;
        $event_end = $schedule->end_time;
        $start_day = $schedule->start_date;
        $end_day = $schedule->end_date;
        
        // 終日予定に時間を入れる
        if ($event_start == NULL)
        {
            $event_start = "00:00:00";
            $event_end = "24:00:00";
        }
        
        // 予定を日にちごとに分解
        $event_list = [];
        
        if ($start_day == $end_day){
            
            // 日をまたがない予定
            $event_list[] = [
                'event_day' => $start_day,
                'start_time' => $event_start,
                'end_time' => $event_end
            ];
            
        } else {
            
            // 日をまたぐ予定
            // 初日
            $event_list[] = [
                    'event_day' => $start_day,
                    'start_time' => $event_start,
                    'end_time' => "24:00:00"
                ];
            
            // 2日目以降
            $start_day = Carbon::createMidnightDate($start_day);
            $end_day = Carbon::createMidnightDate($end_day);

            $count_day = $start_day->diffInDays($end_day);
            
            for ($i=1; $i<$count_day; $i++)
            {
                $next_day = $start_day->addDays($i);
                
                $event_list[] = [
                    // 時間を削除し、日付のみ取得
                    'event_day' => $next_day->toDateString(),
                    'start_time' => "00:00:00",
                    'end_time' => "24:00:00"
                ];
                
            }
            
            // 最終日
            $event_list[] = [
                    'event_day' => $end_day->toDateString(),
                    'start_time' => "00:00:00",
                    'end_time' => $event_end
                ];
            
        }
        
        // 調整期間外の予定を削除
        for ($i=0; $i<count($event_list); $i++)
        {
            // 削除する配列の場所を特定し、削除
            if ($event_list[$i]['event_day']." 00:00:00" < $adjust_start_day
                || $event_list[$i]['event_day']." 00:00:00" > $adjust_end_day) {
                    
                unset($event_list[$i]);
            }
        }
        // 空いた配列を詰める
        $event_list = array_values($event_list);
        
        return $event_list;
        
    }
    
    // 予定をbitに変換する関数
    public static function scheduleToBit($start_time, $end_time, $schedule)
    {
        $start_time = Carbon::createFromTimeString($start_time);
        $end_time = Carbon::createFromTimeString($end_time);
        
        // 調整時間のBit数を計算
        $count = floor($start_time->diffInMinutes($end_time)/30);
            
        // 30分ごとの時間を入れる変数をセット
        $day_point = $start_time;
            
        // $scheduleを再セット
        $event_day = $schedule['event_day'];
        $event_start = Carbon::createFromTimeString($schedule['start_time']);
        $event_end = Carbon::createFromTimeString($schedule['end_time']);
            
        // 予定をbitに変換する
        $bit_data = "";
        for ($i=1; $i<=$count; $i++)
        {
            // 30分ごとに予定がある時間帯化を判断
            if ($event_start <= $day_point && $day_point < $event_end){
                    
                // 予定あり
                $bit_data .= "1";
                    
            } else {
                    
                // 予定なし
                $bit_data .= "0";
            }
                
            // $day_pointを30分ずらす
            $day_point->addMinutes(30);
        }
            
        $bit_schedule[$event_day] = $bit_data;
            
        return $bit_schedule;
    }
    
    // 同日の予定を論理和でまとめる
    public static function takeLogicalSum($bits1, $bits2)
    {
        $logicalSum = "";
        for ($i=0; $i<strlen($bits1); $i++)
        {
            // bitsのi番目の文字を取り出す
            $bit1 = substr($bits1, $i, 1);
            $bit2 = substr($bits2, $i, 1);
            
            // 両方に予定がなければo、他は1で返す
            if ($bit1 == "0" && $bit2 == "0"){
                $logicalSum .= "0";
            } else {
                $logicalSum .= "1";
            }
        }
        
        return $logicalSum;
    }
    
    // 予定がある日の空いている時刻を導き出す
    public static function getFreeTime($start_time, $end_time, $bit_schedule_list)
    {
        // 空いている時刻を配列に入れる
        $free_times = [];
        foreach ($bit_schedule_list as $day=>$bits)
        {
            $day_start = Carbon::createFromTimeString($start_time);
            $day_end = Carbon::createFromTimeString($end_time);
            
            // 調整時間のBit数を計算
            $count = floor($day_start->diffInMinutes($day_end)/30);
            // 30分ごとの時間を入れる変数をセット
            $day_point = $day_start;
            
            $free_time_start = "";
            $free_time_end = "";
            
            // 0,1を判断して空いている時間を配列に入れる
            for ($i=0; $i<strlen($bits); $i++)
            {
                // bitsのi番目の文字を取り出す
                $bit = substr($bits, $i, 1);
            
                if ($bit == "0" && $free_time_start == NULL && $i+1 == strlen($bits)) {
                    
                    // 空き時間の開始と日程調整時間の終了
                    $free_time_start = $day_point->format('H:i');
                    $free_time_end = $day_point->addMinutes(30)->format('H:i');
                    $free_times[$day][] = 
                        "{$free_time_start}~{$free_time_end}";
                    
                } elseif ($bit == "0" && $free_time_start == NULL) {
                    
                    // 空き時間の開始
                    $free_time_start = $day_point->format('H:i');
                    
                } elseif ($bit == "1" && $free_time_start != NULL) {
                    
                    // 空き時間の終わり
                    $free_time_end = $day_point->format('H:i');
                    $free_times[$day][] = 
                        "{$free_time_start}~{$free_time_end}";
                        
                    $free_time_start = "";
                    $free_time_end = "";
                    
                } elseif ($bit == "0" && $free_time_start != NULL && $i+1 == strlen($bits)) {
                    
                    // 空き時間と日程調整時間の終了
                    $free_time_end = $day_point->addMinutes(30)->format('H:i');
                    $free_times[$day][] = 
                        "{$free_time_start}~{$free_time_end}";
                } 
                
                $day_point = $day_point->addMinutes(30);
            }
            
            // 予定がある日に空き時間がない場合
            if (!array_key_exists($day, $free_times)) {
                
                $free_times[$day][] = "";
            }
        }
        
        return $free_times;
    }
}
