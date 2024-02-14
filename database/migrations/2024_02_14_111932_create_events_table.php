<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->comment('イベント作成者');
            $table->string('title')->comment('イベント名');
            $table->string('body')->nullable()->comment('イベント内容');
            $table->date('start_date')->comment('調整開始日');
            $table->date('end_date')->comment('調整終了日');
            $table->datetime('deadline')->comment('回答期限');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('events');
    }
};
