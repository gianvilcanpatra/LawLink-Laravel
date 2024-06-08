<?php

namespace App\Models;

use App\Models\User;
use App\Models\Lawyer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Reviews extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'law_id',
        'ratings',
        'reviews',
        'reviewed_by',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function lawyer()
    {
        return $this->belongsTo(Lawyer::class);
    }
}
