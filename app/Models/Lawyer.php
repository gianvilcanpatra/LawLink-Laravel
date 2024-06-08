<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lawyer extends Model
{
    use HasFactory;

    //these are fillable input
    protected $fillable = [
        'law_id',
        'category',
        'patients',
        'experience',
        'bio_data',
        'status',
    ];

    //state this is belong to user table
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviews()
    {
        return $this->hasMany(Reviews::class);
    }
}
