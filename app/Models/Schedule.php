<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Schedule extends Model
{
    use HasFactory;
    use SoftDeletes;
    
    protected $fillable = [
        'user_id',
        'title',
        'body',
        'start_date',
        'end_date',
        'start_time',
        'end_time'
    ];
    
    //usersに対するリレーション（１対多）
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
