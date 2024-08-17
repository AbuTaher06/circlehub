<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use paginate;

class UserController extends Controller
{
    public function index()
    {
        $users=User::paginate(1);

       return view('user.index', compact('users'));
    }
}
