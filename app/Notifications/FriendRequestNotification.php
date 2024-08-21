<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class FriendRequestNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $friendship;

    public function __construct($friendship)
    {
        $this->friendship = $friendship;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'message' => 'You have received a friend request from ' . $this->friendship->user->name,
            'friendship_id' => $this->friendship->id,
            'sender_id' => $this->friendship->user_id,
        ];
    }
}
