<!-- resources/views/users/index.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="container mx-auto p-4">
        <h2 class="text-xl font-semibold mb-4">Users List</h2>

        <!-- Display Users -->
        <div class="bg-white shadow-md rounded-lg p-4">
            @foreach ($users as $user)
                <div class="mb-4">
                    <h3 class="text-lg font-semibold">{{ $user->name }}</h3>
                    <p>{{ $user->email }}</p>
                    <p>Created {{ $user->created_at->diffForHumans() }}</p>
                    <p>Updated {{ $user->updated_at->diffForHumans() }}</p>
                    <p class="border-t border-gray-300 pt-2"></p>
                </div>
            @endforeach
        </div>

        <!-- Pagination Links -->
        <div class="mt-4">
            {{ $users->links() }}
        </div>
    </div>
@endsection
