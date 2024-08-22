<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Friendship;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;
use App\Notifications\FriendRequestNotification;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProfileController extends Controller
{
    // Display the user's profile page.
    public function show(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $posts = $user->posts()->orderBy('created_at', 'desc')->get();

        $authUser = Auth::user();
        $activities = $authUser->activities()->orderBy('created_at', 'desc')->get();
        $mutualFriends = $authUser->mutualFriends($user);
        $recentlyAddedFriends = $user->recentlyAddedFriends();

        $friendRequestStatus = 'none'; // Default status

        if ($authUser) {
            $friendship = Friendship::where(function ($query) use ($authUser, $user) {
                $query->where('user_id', $authUser->id)
                      ->where('friend_id', $user->id);
            })->orWhere(function ($query) use ($authUser, $user) {
                $query->where('user_id', $user->id)
                      ->where('friend_id', $authUser->id);
            })->first();

            if ($friendship) {
                if ($friendship->status === 'accepted') {
                    $friendRequestStatus = 'friends';
                } elseif ($friendship->status === 'pending') {
                    $friendRequestStatus = ($friendship->user_id == $authUser->id) ? 'sent' : 'pending';
                }
            }
        }

        return view('profile.show', [
            'user' => $user,
            'posts' => $posts,
            'activities' => $activities,
            'mutualFriends' => $mutualFriends,
            'recentlyAddedFriends' => $recentlyAddedFriends,
            'friendRequestStatus' => $friendRequestStatus,
        ]);
    }


    private function getFriendRequestStatus($authUser, $user)
    {
        $friendship = Friendship::where(function ($query) use ($authUser, $user) {
            $query->where('user_id', $authUser->id)
                  ->where('friend_id', $user->id);
        })->orWhere(function ($query) use ($authUser, $user) {
            $query->where('user_id', $user->id)
                  ->where('friend_id', $authUser->id);
        })->first();

        if ($friendship) {
            return $friendship->status;
        } elseif (Friendship::where('user_id', $authUser->id)
            ->where('friend_id', $user->id)
            ->where('status', 'pending')
            ->exists()) {
            return 'sent';
        }

        return 'none';
    }

    // Handle adding a friend request
    public function addFriend(Request $request, User $user)
    {
        // Prevent sending a request to oneself
        if ($user->id === Auth::id()) {
            return back()->withErrors(['error' => 'You cannot add yourself as a friend.']);
        }

        // Check if a friendship already exists
        if ($this->friendshipExists(Auth::id(), $user->id)) {
            return back()->withErrors(['error' => 'You are already friends or a request already exists.']);
        }

        // Create a new friendship request
        $friendship = Friendship::create([
            'user_id' => Auth::id(),
            'friend_id' => $user->id,
            'status' => 'pending'
        ]);

        // Notify the user
        $user->notify(new FriendRequestNotification($friendship));

        return back()->with('status', 'Friend request sent.');
    }

    public function notifications()
    {
        $notifications = Auth::user()->notifications()->orderBy('created_at', 'desc')->get();

        // Optionally mark notifications as read here
        Auth::user()->unreadNotifications->markAsRead();

        return view('notifications.index', compact('notifications'));
    }

    public function showNotifications()
{
    $notifications = Auth::user()->unreadNotifications;

    return view('notifications.index', compact('notifications'));
}
    private function friendshipExists($userId, $friendId)
    {
        return Friendship::where(function ($query) use ($userId, $friendId) {
            $query->where('user_id', $userId)
                  ->where('friend_id', $friendId);
        })->orWhere(function ($query) use ($userId, $friendId) {
            $query->where('user_id', $friendId)
                  ->where('friend_id', $userId);
        })->exists();
    }

    public function confirmRequest(Request $request, $id)
    {
        try {
            $friendship = Friendship::where('user_id', $id)
                                    ->where('friend_id', Auth::id())
                                    ->where('status', 'pending')
                                    ->firstOrFail();

            $friendship->status = 'accepted';
            $friendship->save();

            // Optionally create a reciprocal friendship
            Friendship::updateOrCreate([
                'user_id' => Auth::id(),
                'friend_id' => $id,
            ], ['status' => 'accepted']);

            return back()->with('status', 'Friend request accepted.');
        } catch (ModelNotFoundException $e) {
            return back()->withErrors(['error' => 'Friend request not found.']);
        }
    }

    public function dashboard()
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

    return view('dashboard', [
        'friends' => $friends,
    ]);
}


    public function deleteRequest(Request $request, $id)
    {
        // Delete the friendship request from both directions
        $deleted = Friendship::where(function ($query) use ($id) {
            $query->where('user_id', Auth::id())
                  ->where('friend_id', $id);
        })->orWhere(function ($query) use ($id) {
            $query->where('user_id', $id)
                  ->where('friend_id', Auth::id());
        })->delete();

        if ($deleted) {
            return back()->with('status', 'Friend request deleted.');
        } else {
            return back()->withErrors(['error' => 'Friend request not found.']);
        }
    }

    public function update(Request $request){
        $user=Auth::user();

        $request->validate([
            'profile'=>'nullable|image|max:2048',
            'cover_photo'=>'nullable|image|max:2048',
        ]);

        if($request->hasFile('cover_photo')){
            if($user->cover_photo){
                Storage::delete($user->cover_photo);
            }
            $user->cover_photo=$request->file('cover_photo')->store('cover_photos');
        }
        if($request->hasFile('profile_picture')) {
            if ($user->profile) {
                Storage::delete($user->profile);
            }
            $user->profile = $request->file('profile_picture')->store('profile');
        }
        $user->save();
        return redirect()->route('profile.show', $user->id)->with('status', 'Profile updated!');
    }
    public function editCover(User $user)
    {
        // Make sure the user is authorized to edit this profile
        if (Auth::id() !== $user->id) {
            abort(403);
        }

        return view('profile.edit-cover', compact('user'));
    }

    public function updateCover(Request $request, User $user)
    {
        // Make sure the user is authorized to edit this profile
        if (Auth::id() !== $user->id) {
            abort(403);
        }

        $request->validate([
            'cover_photo' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('cover_photo')) {
            $path = $request->file('cover_photo')->store('cover_photos');
            $user->cover_photo = $path;
            $user->save();
        }

        return redirect()->route('profile.show', $user->id)->with('success', 'Cover photo updated successfully.');
    }
}
