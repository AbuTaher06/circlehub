<?php

namespace App\Http\Controllers;

use Log;
use App\Models\User;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{

    public function index()
{
    // Retrieve friends (users who have messaged the authenticated user or vice versa)
    $friends = User::whereIn('id', function($query) {
        $query->select('receiver_id')
              ->from('messages')
              ->where('sender_id', Auth::id())
              ->distinct();
    })->orWhereIn('id', function($query) {
        $query->select('sender_id')
              ->from('messages')
              ->where('receiver_id', Auth::id())
              ->distinct();
    })->get();

    // Retrieve the latest message for each friend
    $latestMessages = [];

    foreach ($friends as $friend) {
        $latestMessage = Message::where(function($query) use ($friend) {
            $query->where('sender_id', $friend->id)
                  ->orWhere('receiver_id', $friend->id);
        })->latest()->first(); // Get the latest message for this friend

        if ($latestMessage) {
            $latestMessages[] = [
                'friend' => $friend,
                'message' => $latestMessage
            ];
        }
    }

    // Sort latest messages by the timestamp (assuming you have a 'created_at' attribute)
    usort($latestMessages, function($a, $b) {
        return $b['message']->created_at <=> $a['message']->created_at;
    });

    return view('messages.index', compact('latestMessages'));
}

    // Fetch messages between the authenticated user and the specified user
    public function getMessages($userId)
    {
        $messages = Message::where(function($query) use ($userId) {
            $query->where('sender_id', Auth::id())
                  ->where('receiver_id', $userId);
        })->orWhere(function($query) use ($userId) {
            $query->where('sender_id', $userId)
                  ->where('receiver_id', Auth::id());
        })->orderBy('created_at', 'asc')->get();

        $chatUser = User::findOrFail($userId);

        return view('messages.chat', compact('messages', 'chatUser'));
    }

    // Send a message to a specified user
    public function sendMessage(Request $request ) {
        // Validate the request
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string|max:255',
        ]);


            // Create the message
            $message = Message::create([
                'sender_id' => Auth::id(),
                'receiver_id' => $request->receiver_id,
                'message' => $request->message,
                'is_read' => false,
            ]);

            $message->save();

            // Return the created message as JSON
         //   return response()->json($message);

         return redirect()->back();


    }

    // Check for new unread messages
    public function checkNewMessages()
    {
        $newMessages = Message::where('receiver_id', Auth::id())
                              ->where('is_read', false)
                              ->get();

        // Mark messages as read
        Message::whereIn('id', $newMessages->pluck('id'))->update(['is_read' => true]);

        return response()->json($newMessages);
    }

    // Delete a specified message
    public function deleteMessage($id)
    {
        $message = Message::where('id', $id)
                          ->where('sender_id', Auth::id())
                          ->first();

        if ($message) {
            $message->delete();
            return response()->json(['success' => true, 'message' => 'Message deleted successfully.']);
        }

        return response()->json(['success' => false, 'message' => 'Message not found or you are not authorized to delete this message.'], 404);
    }
}
