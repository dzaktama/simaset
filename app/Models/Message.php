<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    // Kolom-kolom yang boleh diisi datanya secara massal.
    // 'body' itu isi pesannya, 'asset_id' itu kalau ada lampiran barang.
    protected $fillable = ['conversation_id', 'sender_id', 'body', 'is_read', 'asset_id'];

    // Pesan ini milik Percakapan (Conversation) mana?
    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    // Siapa pengirim pesan ini? (User)
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    // Kalau pesan ini lampiran aset, aset yang mana?
    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }
}
