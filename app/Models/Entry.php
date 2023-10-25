<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Entry extends Model
{
    protected $fillable = [
        'title',
        'content'
    ];

    public function author()
    {
        return $this->belongsTo(User::class);
    }
}
