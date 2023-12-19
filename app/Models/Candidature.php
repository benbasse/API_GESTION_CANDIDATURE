<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Candidature extends Model
{
    use HasFactory;

    public function formation()
    {
        return $this->belongsToMany(Formation::class);
    }

    public function user()
    {
        return $this->belongsToMany(User::class);
    }

    protected $fillable = [
        'formation_id',
        'user_id'
    ];
}
