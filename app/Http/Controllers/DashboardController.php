<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // Fetch all posts with related counts
        $posts = Post::withCount('likes', 'shares', 'comments')
                     ->inRandomOrder() // Randomize posts
                     ->get();

        // Get the authenticated user
        $authUser = Auth::user();

        // Fetch all friends for the authenticated user, excluding themselves
        $friends = User::whereHas('friendships', function ($query) use ($authUser) {
            $query->where('status', 'accepted')
                  ->where(function ($query) use ($authUser) {
                      $query->where('user_id', $authUser->id)
                            ->orWhere('friend_id', $authUser->id);
                  });
        })->where('id', '!=', $authUser->id)->get();

        // Fetch all users who are not friends and exclude the authenticated user
        $nonFriends = User::where(function ($query) use ($authUser) {
            $query->whereDoesntHave('friendships', function ($query) use ($authUser) {
                $query->where('status', 'accepted')
                      ->where(function ($query) use ($authUser) {
                          $query->where('user_id', $authUser->id)
                                ->orWhere('friend_id', $authUser->id);
                      });
            });
        })->where('id', '!=', $authUser->id)->get();

        // Return the dashboard view with the posts, friends, and non-friends data
        return view('dashboard', [
            'posts' => $posts,
            'friends' => $friends,
            'nonFriends' => $nonFriends,
        ]);
    }
}
