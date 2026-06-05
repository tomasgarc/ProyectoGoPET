<?php

namespace App\Http\Controllers;

use App\Models\CareRequest;
use App\Models\Chat;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    /**
     * Display the Wallapop-style chat inbox.
     */
    public function index(Request $request)
    {
        $userId = auth()->id();

        // Get all chats where user is owner or caregiver, with latest messages and relations
        $chats = Chat::where(function ($query) use ($userId) {
            $query->where('user_id', $userId)
                ->orWhere('creator_id', $userId);
        })
            ->with(['careRequest.dogs', 'user', 'creator', 'latestMessage'])
            ->get()
            ->sortByDesc(function ($chat) {
                return $chat->latestMessage ? $chat->latestMessage->created_at : $chat->created_at;
            });

        $activeChat = null;
        if ($request->has('chat')) {
            $activeChat = Chat::with(['messages.sender', 'careRequest.dogs', 'user', 'creator'])
                ->where(function ($query) use ($userId) {
                    $query->where('user_id', $userId)
                        ->orWhere('creator_id', $userId);
                })
                ->find($request->chat);

            if ($activeChat) {
                // Mark messages from partner as read
                $activeChat->messages()
                    ->where('sender_id', '!=', $userId)
                    ->whereNull('read_at')
                    ->update(['read_at' => now()]);
            }
        }

        return view('chats.index', compact('chats', 'activeChat'));
    }

    /**
     * Start a new chat or resume an existing one for a Care Request.
     */
    public function start(CareRequest $careRequest)
    {
        $userId = auth()->id();

        // You cannot chat with yourself
        if ($careRequest->user_id === $userId) {
            return back()->with('error', 'No puedes abrir un chat sobre tu propia petición.');
        }

        // Ensure the care request is active
        if ($careRequest->isFinalized()) {
            return back()->with('error', 'Esta petición ha finalizado y no acepta más mensajes.');
        }

        // Check if chat already exists
        $chat = Chat::firstOrCreate([
            'care_request_id' => $careRequest->id,
            'user_id' => $userId,
        ], [
            'creator_id' => $careRequest->user_id,
        ]);

        return redirect()->route('chats.index', ['chat' => $chat->id]);
    }

    /**
     * Send a message within a chat.
     */
    public function storeMessage(Request $request, Chat $chat)
    {
        $userId = auth()->id();

        // Verify the user is part of the chat
        if ($chat->user_id !== $userId && $chat->creator_id !== $userId) {
            abort(403);
        }

        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        // If care request has finished, prevent further messages
        if ($chat->careRequest && $chat->careRequest->isFinalized()) {
            return back()->with('error', 'No se pueden enviar mensajes a peticiones que han finalizado.');
        }

        Message::create([
            'chat_id' => $chat->id,
            'sender_id' => $userId,
            'content' => $request->content,
        ]);

        return redirect()->route('chats.index', ['chat' => $chat->id])->with('message_sent', true);
    }

    /**
     * Start a direct chat with a user (e.g. from admin panel).
     */
    public function startDirectChat(User $user)
    {
        $userId = auth()->id();

        // You cannot chat with yourself
        if ($user->id === $userId) {
            return back()->with('error', 'No puedes abrir un chat contigo mismo.');
        }

        // Check if a direct chat (where care_request_id is null) already exists between these two users
        $chat = Chat::whereNull('care_request_id')
            ->where(function ($query) use ($userId, $user) {
                $query->where(function ($q) use ($userId, $user) {
                    $q->where('user_id', $userId)->where('creator_id', $user->id);
                })->orWhere(function ($q) use ($userId, $user) {
                    $q->where('user_id', $user->id)->where('creator_id', $userId);
                });
            })
            ->first();

        if (!$chat) {
            $chat = Chat::create([
                'care_request_id' => null,
                'user_id' => $user->id, // recipient
                'creator_id' => $userId, // creator
            ]);
        }

        return redirect()->route('chats.index', ['chat' => $chat->id]);
    }
}
