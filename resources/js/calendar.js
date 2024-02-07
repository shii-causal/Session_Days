//Full calendarの読み込み
import axios from "axios";
import { Calendar } from "@fullcalendar/core";
import dayGridPlugin from "@fullcalendar/daygrid";
import timeGridPlugin from '@fullcalendar/timegrid';

// カレンダーを表示させたいタグのidを取得
var calendarEl = document.getElementById("calendar");

//id=calendarがあるblade.phpファイルで実行
if (calendarEl !== null) {
    
    // new Calender(カレンダーを表示させたいタグのid, {各種カレンダーの設定});
    let calendar = new Calendar(calendarEl, {
    
        // プラグインの導入
        plugins: [dayGridPlugin, timeGridPlugin],
    
        // カレンダー表示
        initialView: "dayGridMonth", // 最初に表示させるページの形式
        headerToolbar: { // ヘッダーの設定
        // コンマのみで区切るとページ表示時に間が空かず、半角スペースで区切ると間が空く
        start: "prev,next today", // ヘッダー左（前月、次月、今日の順番で左から配置）
        center: "title", // ヘッダー中央（今表示している月、年）
        end: "dayGridMonth,timeGridWeek", // ヘッダー右（月形式）
        },
        
        //高さをウィンドウのサイズにそろえる
        height: "auto", 
        //カレンダーを日本語で表示
        locale: "ja",
    
    });

    calendar.render();
}