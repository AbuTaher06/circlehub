<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Friendship;
use Illuminate\Support\Facades\Auth;

class FriendController extends Controller
{
    /**
     * Send a friend request to another user.
     */

     public function showFriends()
     {
         $authUser = Auth::user();

         // Fetch all friends for the authenticated user
         $friends = User::whereHas('friendships', function ($query) use ($authUser) {
             $query->where('status', 'accepted')
                   ->where(function ($query) use ($authUser) {
                       $query->where('user_id', $authUser->id)
                             ->orWhere('friend_id', $authUser->id);
                   });
         })->get();
  

         // Return the dashboard view with the friends data
         return view('dashboard', [
             'friends' => $friends,
         ]);
     }

}


    /**
     * Show user profile with posts and friend request status.



     */
