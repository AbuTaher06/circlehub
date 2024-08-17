<!-- resources/views/profile/show.blade.php -->
@extends('layouts.app')

@section('content')
<div class="flex flex-col min-h-screen bg-gray-900 text-white">
    <!-- Profile Header -->
    <header class="bg-gray-800 p-4 shadow-md">
        <div class="container mx-auto flex flex-col items-center">
            <img src="{{ asset('uploads/' . $user->profile) }}" alt="Profile Picture" class="w-32 h-32 rounded-full mb-4">
            <h1 class="text-3xl font-bold">{{ $user->name }}</h1>
        </div>
    </header>

    <!-- User Activities -->
    <main class="container mx-auto p-4 bg-gray-800">
        <h2 class="text-xl font-semibold mb-4">Recent Activities</h2>
        {{-- <div class="space-y-4">
            @forelse($activities as $activity)
                <div class="bg-gray-700 p-4 rounded-lg">
                    <p class="text-gray-300">{{ $activity->description }}</p>
                    <span class="text-gray-500 text-sm">{{ $activity->created_at->format('F j, Y, g:i a') }}</span>
                </div>
            @empty
                <p class="text-gray-500">No activities found.</p>
            @endforelse
        </div> --}}
    </main>
</div>
@endsection
