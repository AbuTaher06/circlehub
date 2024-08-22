<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Storage;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    // Show the form for creating a new post
    public function create()
    {
        return view('posts.create');
    }

    // Store a newly created post in storage
    public function store(Request $request ,Post $post)
    {
        $validateData = $request->validate([
            'post_content' => 'nullable|string',
            'media' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,mp4,mov,avi|max:20480',
        ]);

        $mediaPath = null;
        if ($request->hasFile('media')) {
            $mediaPath = $request->file('media')->store('public/media');
        }

        $post->create([
            'user_id' => Auth::id(),
            'content' => $validateData['post_content'],
            'media_path' => $mediaPath,
        ]);

        // Redirect to dashboard


        //Log activity
        Activity::create([
            'user_id' => Auth::id(),
            'type' => 'post',
            'description' => 'Created a new post',
            ]);

        return redirect()->route('dashboard')->with('success', 'Post created successfully.');
    }

    // Show the form for editing the specified post
    public function edit(Post $post)
    {
        return view('posts.edit', compact('post'));
    }

    // Update the specified post in storage
    public function update(Request $request, Post $post)
    {
        $validateData = $request->validate([
            'post_content' => 'nullable|string',
            'media' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,mp4,mov,avi|max:20480',
        ]);

        if ($request->hasFile('media')) {
            // Delete old media file if exists
            if ($post->media_path) {
                \Storage::delete($post->media_path);
            }
            $mediaPath = $request->file('media')->store('public/media');
            $post->update(['media_path' => $mediaPath]);
        }

        $post->update([
            'content' => $validateData['post_content'],
        ]);

        return redirect()->route('dashboard')->with('success', 'Post updated successfully.');
    }

    // Remove the specified post from storage
    public function destroy(Post $post)
    {
        // Delete media file if exists
        if ($post->media_path) {
            \Storage::delete($post->media_path);
        }
        $post->delete();

        return redirect()->route('dashboard')->with('success', 'Post deleted successfully.');
    }

    // Show all posts (if needed)
    public function show($id)
{
$post = Post::with('user', 'likes', 'comments.user')->findOrFail($id);
    return view('posts.show', compact('post'));
}
public function report($id)
{
    $post = Post::findOrFail($id);
    // Implement the logic to report the post (e.g., save to a reports table, send notification, etc.)

    return redirect()->back()->with('message', 'Post reported successfully.');
}


}
