<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = ['type']; // Tipe chat, misalnya 'private' atau 'group' (future dev)

    // Satu percakapan punya BANYAK pesan.
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    // Detail siapa aja peserta chat ini.
    public function participants()
    {
        return $this->hasMany(ConversationParticipant::class);
    }

    // Ambil data User yang terlibat langsung.
    public function users()
    {
        return $this->belongsToMany(User::class, 'conversation_participants', 'conversation_id', 'user_id');
    }
}
