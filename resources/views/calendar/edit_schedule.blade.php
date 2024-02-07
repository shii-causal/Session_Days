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
        
        </body>
    </x-app-layout>
</html>
