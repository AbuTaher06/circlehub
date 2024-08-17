<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Post;

class DashboardController extends Controller
{
    public function index()
    {
        $posts = Post::withCount('likes', 'shares', 'comments')
                    
                     ->inRandomOrder() // Randomize posts
                     ->get();

        return view('dashboard', compact('posts'));
    }
}
