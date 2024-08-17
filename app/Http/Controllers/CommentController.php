<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Comment;  // Correct import
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(Request $request, $postId)
    {
        $request->validate([
            'comment_content' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:comments,id', // For replies
        ]);

        $post = Post::findOrFail($postId);

        Comment::create([
            'user_id' => Auth::user()->id,
            'post_id' => $postId,
            'content' => $request->comment_content,
            'parent_id' => $request->parent_id,
        ]);

        return back();
    }
}
