<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
}
