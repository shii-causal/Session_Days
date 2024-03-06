<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>イベント参加希望</title>

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
            
            <h1>出席登録画面</h1>
            
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
            
            <!--イベント参加登録-->
            <div class="attend">
                <form action="/events/attend/{{ $event->id }}/{{ Auth::user()->id }}" name="attend" method="POST">
                    @csrf
                    <button type="button" onclick="attendEvent()">参加する</button>
                </form>
            </div>
            
            <!--ホーム画面に戻る-->
            <a href="#">戻る</a>
        
        <script>
            function attendEvent() {
                'use strict'
        
                if (confirm('このイベントに参加希望を出しますか？')) {
                    document.attend.submit();
                }
            }
        </script>
        </body>
    </x-app-layout>
</html>