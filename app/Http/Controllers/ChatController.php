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
     * Display the chat inbox (Main UI).
     */
    public function index()
    {
        // Get all users for the 'New Chat' list (exclude self)
        $users = User::where('id', '!=', Auth::id())->get();

        // Get existing conversations for the current user (Recent Chats)
        // This is a bit complex in Eloquent, so raw/builder is often easier, 
        // but let's try a clean Eloquent approach.
        $recentChats = Conversation::whereHas('participants', function($q) {
                $q->where('user_id', Auth::id());
            })
            ->with(['users' => function($q) {
                $q->where('users.id', '!=', Auth::id()); // Load the 'other' user
            }, 'messages' => function($q) {
                $q->latest()->limit(1); // Load last message for preview
            }])
            ->get()
            ->sortByDesc(function($conversation) {
                return $conversation->messages->first()->created_at ?? $conversation->created_at;
            });

        return view('chat.index', [
            'title' => 'Internal Chat',
            'users' => $users,
            'recentChats' => $recentChats
        ]);
    }

    /**
     * Fetch messages for a specific user (AJAX).
     * If conversation doesn't exist, returns empty structure.
     */
    public function getConversation($otherUserId)
    {
        $myId = Auth::id();

        // Find private conversation between Me and OtherUser
        $conversation = Conversation::where('type', 'private')
            ->whereHas('participants', function($q) use ($myId) {
                $q->where('user_id', $myId);
            })
            ->whereHas('participants', function($q) use ($otherUserId) {
                $q->where('user_id', $otherUserId);
            })
            ->with(['messages.sender', 'messages.asset', 'users'])
            ->first();

        if (!$conversation) {
            return response()->json([
                'status' => 'new',
                'messages' => []
            ]);
        }

        // Mark unread messages as read
        Message::where('conversation_id', $conversation->id)
            ->where('sender_id', '!=', $myId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json([
            'status' => 'found',
            'conversation_id' => $conversation->id,
            'messages' => $conversation->messages
        ]);
    }

    /**
     * Send a message (AJAX).
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'body' => 'nullable|string|max:1000',
            'asset_id' => 'nullable|exists:assets,id',
        ]);

        if (empty($request->body) && empty($request->asset_id)) {
            return response()->json(['error' => 'Message cannot be empty'], 422);
        }

        $myId = Auth::id();
        $receiverId = $request->receiver_id;

        DB::beginTransaction();
        try {
            // Check if conversation exists (Use same logic as getConversation or extract to helper)
            // ... (Logic kept same for brevity)
            $conversation = Conversation::where('type', 'private')
                ->whereHas('participants', function($q) use ($myId) {
                    $q->where('user_id', $myId);
                })
                ->whereHas('participants', function($q) use ($receiverId) {
                    $q->where('user_id', $receiverId);
                })
                ->first();

            // If not, create new
            if (!$conversation) {
                $conversation = Conversation::create(['type' => 'private']);
                $conversation->participants()->create(['user_id' => $myId]);
                $conversation->participants()->create(['user_id' => $receiverId]);
            }

            // Create message
            $message = $conversation->messages()->create([
                'sender_id' => $myId,
                'body' => $request->body,
                'asset_id' => $request->asset_id
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $message->load('sender', 'asset')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
