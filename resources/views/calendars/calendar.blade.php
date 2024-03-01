<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>カレンダー</title>

        <!-- Fonts -->
        <link href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

        <!-- Styles -->
        <style>
            body {
                font-family: 'Nunito', sans-serif;
            }
        </style>
        
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
    </head>
    
    <x-app-layout>
        <body>
            
            <!--カレンダーの表示-->
            <div id='calendar'></div>
            
            <!--予定追加モーダル-->
            <div id="modal-add" class="modal">
                <div class="modal-contents">
                    <form method="POST" action="{{ route('create') }}">
                        @csrf
                        <input id="user_id" name="user_id" type="hidden" value="{{ Auth::user()->id }}"/>
                        <label for="title">イベント名</label>
                        <input id="title" class="input-title" type="text" name="title" value=""/>
                        <label for="start_date">開始日</label>
                        <input id="start_date" class="input-start_date input-date" type="date" name="start_date" value=""/>
                        <label for="end_date">終了日</label>
                        <input id="end_date" class="input-end_date date input-date" type="date" name="end_date" value=""/><br>
                        <label for="all_day">終日</label>
                        <input id="all_day" class="all_day" type="checkbox" name="all_day" value"true"/><br><br>
                        
                        <div class="time">
                            <label for="start_time">開始時刻</label>
                            <input id="start_time" class="input-start_time input-time" type="time" step="60" name="start_time" value=""/>
                            <label for="end_time">終了時刻</label>
                            <input id="end_time" class="input-end_time input-time" type="time" step="60" name="end_time" value=""/><br>
                        </div>
                        
                        <label for="body">イベント内容</label>
                        <textarea id="body" rows="3" name="body" value=""></textarea>
                        <button type="button" onclick="closeAddModal()">キャンセル</button>
                        <button type="submit">決定</button>
                    </form>
                </div>
            </div>
        
        </body>
    </x-app-layout>
</html>

<style scoped>

body {
    box-sizing: border-box;
    margin: 0;
}

/* モーダルのオーバーレイ */
.modal{
    display: none; /* モーダル開くとflexに変更（ここの切り替えでモーダルの表示非表示をコントロール） */
    justify-content: center;
    align-items: center;
    position: absolute;
    z-index: 10; /* カレンダーの曜日表示がz-index=2のため、それ以上にする必要あり */
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    height: 100%;
    width: 100%;
    background-color: rgba(0,0,0,0.5);
}
/* モーダル */
.modal-contents{
    background-color: white;
    height: 450px;
    width: 600px;
    padding: 20px;
}

/* 以下モーダル内要素のデザイン調整 */
input{
    padding: 2px;
    border: 1px solid black;
    border-radius: 5px;
}
.input-title{
    display: block;
    width: 80%;
    margin: 0 0 20px;
}
.input-date{
    width: 27%;
    margin: 0 5px 20px 0;
}
.input-time{
    width: 27%;
    margin: 0 5px 20px 0;
}
textarea{
    display: block;
    width: 80%;
    margin: 0 0 20px;
    padding: 2px;
    border: 1px solid black;
    border-radius: 5px;
    resize: none;
}

/* 終日チェックボックスのチェック時 */
.all_day:checked ~ .time{
    display: none;
}
</style>
