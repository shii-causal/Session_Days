<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>日程調整</title>

        <!-- Fonts -->
        <link href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

        <!-- Styles -->
        <style>
            body {
                font-family: 'Nunito', sans-serif;
            }
        </style>
        
        @vite(['resources/css/app.css'])
        
    </head>
    
    <x-app-layout>
        <body>
            
            <h1>日程調整</h1>
            
            <!--イベント内容-->
            <div class="event">
                <h2>{{ $event->title }}</h2>
                <h3>イベント作成者：{{ $event->user->name }}</h3>
                <h3>日程調整期間：{{ $event->start_date->format('Y年n月j日') }}～{{ $event->end_date->format('Y年n月j日') }}</h3>
                <h3>回答締切：{{ $event->deadline->format('Y年n月j日') }}</h3>
                
                @if ($event->body != NULL)
                    <details>
                        <summary>イベント詳細</summary>
                        {{ $event->body }}
                    </details>
                @endif
            </div>
            
            <!--参加予定者一覧-->
            <div class="attend_user">
                <h2>参加者</h2>
                
                @foreach ($event->users as $user)
                    {{ $user->name }}
                @endforeach
            </div>
            
            <!--日程の絞り込み-->
            <form method="GET" action="{{ route('adjustSchedule') }}">
                @csrf
                <p>
                    <input type="hidden" name="event_id" value="{{ $event->id }}"/>
                    <select name="start_hour">
                        @for ($i=0; $i<=23; $i++)
                            <option value="{{ $i }}">{{ $i }}</option>
                        @endfor
                    </select>
                    ：
                    <select name="start_minute">
                        <option value="00">00</option>
                        <option value="30">30</option>
                    </select>
                    ～
                    <select name="end_hour">
                        @for ($i=0; $i<=24; $i++)
                            <option value="{{ $i }}">{{ $i }}</option>
                        @endfor
                    </select>
                    ：
                    <select name="end_minute">
                        <option value="00">00</option>
                        <option value="30">30</option>
                    </select>
                    の間で
                    <select name="time">
                        @for ($i=1; $i<=24; $i++)
                            <option value="{{ $i }}">{{ $i }}</option>
                        @endfor
                    </select>
                    時間以上空いている日程
                </p>
                <button type="submit" value="adjust_event">抽出する</button>
            </form>
            
            <!--予定が合わないとき-->
            @if (is_string($return))
                <br><p>{{ $return }}</p><br>
            @endif
            
            <!--日程の表示-->
            @if (!is_string($return) && $return != NULL)
            
                <br>
                @foreach ($return as $day=>$return)
                    <p>{{ date('Y年n月j日', strtotime($day)) }}</p>
                    <p>
                            {{ $return }}
                    </p><br>
                @endforeach

            @endif    
            
            <!--ホーム画面に戻る-->
            <a href="#">戻る</a>
        
        </body>
    </x-app-layout>
</html>
