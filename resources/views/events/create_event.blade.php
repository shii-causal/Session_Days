<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>イベント作成</title>

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
            
            @if($url == 0)
                <!--イベント内容入力-->
                <div class="event_form">
                    <form method="POST" action="/events/create">
                        @csrf
                        <input id="user_id" name="user_id" type="hidden" value="{{ Auth::user()->id }}"/>
                        <div class="title">
                            <h2>イベント名</h2>
                            <input type="text" name="title" value=""/>
                        </div>
                        <div class="body">
                            <h2>イベント内容</h2>
                            <textarea rows="3" name="body" value=""></textarea>
                        </div>
                        <div class="date">
                            <h3>日程調整時期</h3>
                            <p>開始日
                                <input type="date" name="start_date" value=""/>
                                終了日
                                <input type="date" name="end_date" value=""/>
                            </P>
                        </div>
                        <div class="deadline">
                            <h2>回答期限</h2>
                            <input type="date" name="deadline_date" value=""/>
                        </div>
                        <button type="submit" value="create_event">作成</button>
                    </form>
                </div>
            @endif
            
            <!--イベント作成後に表示-->
            @if($url != 0)
                <!--参加URL表示-->
                <h1>以下のURLを参加希望者に共有してください</h1>
                <p>有効期限：{{ $event->deadline }}</p>
                <p>{{ $url }}</p>
                
                <!--参加希望画面に移動する-->
                <a href="{{ $url }}">参加希望者一覧に移動する</a><br>
            @endif
            
            <!--ホーム画面に戻る-->
            <a href="#">戻る</a>
        
        </body>
    </x-app-layout>
</html>