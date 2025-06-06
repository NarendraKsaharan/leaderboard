<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Leaderboard extends Model
{
    use HasFactory;

    protected $fillable = [
        "user_id", "total_point", "rank"
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}