<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Share;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShareController extends Controller
{
    public function store($postId)
    {
        $post = Post::findOrFail($postId);
        Share::create([
            'post_id' => $postId,
            'user_id' => Auth::id(),
        ]);
         //Log activity
         Activity::create([
            'user_id' => Auth::id(),
            'type' => 'share',
            'description' => 'you have a new share',
            ]);
    }
}
