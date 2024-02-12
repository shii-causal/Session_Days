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
        
        //予定追加
        customButtons: { // カスタムボタン
            eventAddButton: { // 新規予定追加ボタン
            
                text: '予定を追加',
                
                click: function() {
                    
                    // 初期化（以前入力した値をクリアする）
                    document.getElementById("title").value = "";
                    document.getElementById("start_date").value = "";
                    document.getElementById("end_date").value = "";
                    document.getElementById("start_time").value = "";
                    document.getElementById("end_time").value = "";
                    document.getElementById("body").value = "";
    
                    // 新規予定追加モーダルを開く
                    document.getElementById('modal-add').style.display = 'flex';
                }
            }
        },
        
        headerToolbar: { // ヘッダーの設定
        // コンマのみで区切るとページ表示時に間が空かず、半角スペースで区切ると間が空く
        start: "prev,next today", // ヘッダー左（前月、次月、今日の順番で左から配置）
        center: "title", // ヘッダー中央（今表示している月、年）
        end: "eventAddButton dayGridMonth,timeGridWeek", // ヘッダー右（月形式）
        },
        
        //高さをウィンドウのサイズにそろえる
        height: "auto", 
        //カレンダーを日本語で表示
        locale: "ja",
        
        // DBに登録した予定を表示する
        events: function (info, successCallback, failureCallback) { // eventsはページが切り替わるたびに実行される
            // axiosでLaravelの予定取得処理を呼び出す
            axios
                .post("/calendar/get", {
                    // 現在カレンダーが表示している日付の期間(1月ならば、start_date=1月1日、end_date=1月31日となる)
                    start_date: info.start.valueOf(),
                    end_date: info.end.valueOf(),
                })
                .then((response) => {
                    // 既に表示されているイベントを削除（重複防止）
                    calendar.removeAllEvents(); // ver.6でもどうやら使える（ドキュメントにはない？）
                    // カレンダーに読み込み
                    successCallback(response.data); // successCallbackに予定をオブジェクト型で入れるとカレンダーに表示できる
                })
                .catch((error) => {
                    // バリデーションエラーなど
                    alert("登録に失敗しました。");
                });
        },
    });

    calendar.render();
    
    // 新規予定追加モーダルを閉じる
    window.closeAddModal = function(){
        document.getElementById('modal-add').style.display = 'none';
    }
}