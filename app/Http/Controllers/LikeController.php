<?php
namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    public function toggle($postId)
    {
        $post = Post::findOrFail($postId);
        $like = $post->likes()->where('user_id', Auth::id())->first();

        if ($like) {
            // If already liked, delete the like
            $like->delete();
        } else {
            // If not liked, create a new like
            Like::create([
                'post_id' => $postId,
                'user_id' => Auth::id(),
            ]);
        }

        // Return back without any messages
        return redirect()->back();
    }
}
