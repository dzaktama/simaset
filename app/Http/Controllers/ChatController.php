<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Message;
use App\Models\Conversation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    /**
     * Nampilin Halaman Utama Chat
     * Fungsi ini jalan pas buka menu "Pesan & Diskusi".
     * Tugasnya nyiapin daftar kontak sama chat-chat terakhir biar bisa ditampilin di layar.
     */
    public function index()
    {
        // 1. Ambil daftar semua orang KECUALI diri sendiri.
        // Buat ditampilin di list "New Chat" atau daftar kontak di sebelah kiri.
        // Kan gak mungkin kita chat sama diri sendiri
        $users = User::where('id', '!=', Auth::id())->get();

        // 2. Ambil daftar chat yang udah pernah dilakukan (Recent Chats).
        // Ini agak teknis dikit query-nya pake Eloquent.
        // Intinya: Cari percakapan (Conversation) dimana user yang login ini jadi pesertanya.
        $recentChats = Conversation::whereHas('participants', function($q) {
                $q->where('user_id', Auth::id());
            })
            ->with(['users' => function($q) {
                // Load data lawan bicara kita (selain ID kita sendiri)
                $q->where('users.id', '!=', Auth::id()); 
            }, 'messages' => function($q) {
                // Ambil 1 pesan terakhir aja buat preview di list (misal: "Halo...")
                $q->latest()->limit(1); 
            }])
            ->get()
            // Urutin dari yang paling baru ada chat-nya
            ->sortByDesc(function($conversation) {
                return $conversation->messages->first()->created_at ?? $conversation->created_at;
            });

        // Kirim datanya ke file view (tampilan) -> resources/views/chat/index.blade.php
        return view('chat.index', [
            'title' => 'Internal Chat',
            'users' => $users,
            'recentChats' => $recentChats
        ]);
    }

    /**
     * Ambil Isi Chat (AJAX)
     * Fungsi ini dipanggil pas klik salah satu nama user di daftar kontak.
     * Dia bakal nyari history chat sama orang itu.
     */
    public function getConversation($otherUserId)
    {
        $myId = Auth::id(); // ID yang lagi login

        // Cari percakapan 'private' antara DIA.
        $conversation = Conversation::where('type', 'private')
            ->whereHas('participants', function($q) use ($myId) {
                $q->where('user_id', $myId);
            })
            ->whereHas('participants', function($q) use ($otherUserId) {
                $q->where('user_id', $otherUserId);
            })
            // Sekalian bawa pesan-pesannya, info pengirim, dan kalau ada aset yang di-share
            ->with(['messages.sender', 'messages.asset', 'users'])
            ->first();

        // Kalau belum pernah chat, ya balikin kosong aja.
        if (!$conversation) {
            return response()->json([
                'status' => 'new', // Tandanya ini chat baru
                'messages' => []
            ]);
        }

        // Kalau ada chat yang belum dibaca dari lawan bicara, tandain 'sudah dibaca' (Read).
        Message::where('conversation_id', $conversation->id)
            ->where('sender_id', '!=', $myId) // Pesan dari orang lain
            ->where('is_read', false)
            ->update(['is_read' => true]);

        // Kirim data chat-nya ke frontend biar bisa muncul di layar
        return response()->json([
            'status' => 'found',
            'conversation_id' => $conversation->id,
            'messages' => $conversation->messages
        ]);
    }

    /**
     * Kirim Pesan Baru
     * Ini dipanggil pas tekan tombol kirim (pesawat kertas).
     */
    public function sendMessage(Request $request)
    {
        // Validasi dulu: Penerimanya sapa? Isinya apa?
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'body' => 'nullable|string|max:1000',
            'asset_id' => 'nullable|exists:assets,id',
        ]);

        // Gak boleh kirim pesan kosong melompong (kecuali lagi share aset)
        if (empty($request->body) && empty($request->asset_id)) {
            return response()->json(['error' => 'Waduh, pesannya kosong nih bro.'], 422);
        }

        $myId = Auth::id();
        $receiverId = $request->receiver_id;

        DB::beginTransaction(); // Biar aman, kalau error di tengah jalan, gak ada data nyangkut.
        try {
            // Cek dulu, udah ada "ruang chat" (Conversation) belum sama orang ini?
            // Logic-nya sama kayak di getConversation di atas.
            $conversation = Conversation::where('type', 'private')
                ->whereHas('participants', function($q) use ($myId) {
                    $q->where('user_id', $myId);
                })
                ->whereHas('participants', function($q) use ($receiverId) {
                    $q->where('user_id', $receiverId);
                })
                ->first();

            // Kalau belum ada "ruang chat", kita bikinin dulu yang baru.
            if (!$conversation) {
                $conversation = Conversation::create(['type' => 'private']);
                // Masukin peserta chat: dan Dia.
                $conversation->participants()->create(['user_id' => $myId]);
                $conversation->participants()->create(['user_id' => $receiverId]);
            }

            // Nah, baru simpan pesannya ke tabel messages.
            $message = $conversation->messages()->create([
                'sender_id' => $myId,
                'body' => $request->body,         // Ini teks pesannya
                'asset_id' => $request->asset_id  // Ini ID aset kalo lagi share barang
            ]);

            DB::commit(); // Simpan permanen ke database

            // Balikin respons sukses ke frontend + bawa data pesan barunya buat ditampilin
            return response()->json([
                'success' => true,
                'message' => $message->load('sender', 'asset')
            ]);

        } catch (\Exception $e) {
            DB::rollBack(); // Batalin semua kalau error
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
