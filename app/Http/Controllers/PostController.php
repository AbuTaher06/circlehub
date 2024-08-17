<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public function store(Request $request)
    {
        $validateData=$request->validate([
            'post_content' => 'nullable|string',
            'media' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,mp4,mov,avi|max:20480',
        ]);

       $mediaPath=null;
       if($request->hasFile('media')){
           $mediaPath=$request->file('media')->store('public/media');
       }

      Post::create([
        'user_id' => Auth::user()->id,
        'content' => $validateData['post_content'],
        'media_path' => $mediaPath,
      ]);

      return redirect()->route('dashboard')->with('success', 'Post created successfully.');
    }
    public function index(){
        $posts = Post::withCount('likes','shares','comments')
                ->where('user_id', Auth::user()->id)
                ->latest()
                ->get();
        return view('posts.show', compact('posts'));
    }
}
