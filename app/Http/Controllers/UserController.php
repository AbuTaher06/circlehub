<?php

namespace App\Http\Controllers;

use paginate;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        $users=User::paginate(1);

       return view('user.index', compact('users'));
    }

    public function toggleDarkMode(Request $request)
{
    $user = Auth::user();
    $user->dark_mode = $request->input('dark_mode') === 'true';
    $user->save();

    return response()->json(['success' => true]);
}

}
