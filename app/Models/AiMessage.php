<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiMessage extends Model
{
    protected $fillable = ['chat_id', 'role', 'content'];

    public function chat()
    {
        return $this->belongsTo(AiChat::class, 'chat_id');
    }
}
