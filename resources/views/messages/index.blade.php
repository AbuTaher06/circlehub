@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8">
    <h1 class="text-3xl font-bold mb-6 text-center text-gray-800">Your Messages</h1>

    <!-- Display user conversations -->
    @forelse($latestMessages as $item)
        <div class="bg-white p-4 rounded-lg shadow-md mb-4 flex items-center transition-transform transform hover:scale-105">
            <a href="{{ route('messages.get', ['userId' => $item['friend']->id]) }}" class="flex items-center w-full">
                <img src="{{ asset('uploads/' . $item['friend']->profile) }}" alt="{{ $item['friend']->name }}" class="w-12 h-12 rounded-full border-2 border-blue-500">
                <div class="ml-4 flex-1">
                    <p class="font-semibold text-lg text-blue-600">{{ $item['friend']->name }}</p>
                    @if(Auth::id() !== $item['friend']->id) <!-- Check if the friend is not the authenticated user -->
                        <p class="text-gray-600">{{ $item['message']->sender->name ?? 'No messages yet' }}: <span class="font-medium">{{ $item['message']->message ?? 'No messages yet' }}</span></p>
                    @else
                        <p class="text-gray-600">You: <span class="font-medium">{{ $item['message']->message ?? 'No messages yet' }}</span></p> <!-- Show only "You" for the authenticated user -->
                    @endif
                </div>
                <div class="text-gray-500 text-sm">
                    <p>{{ $item['message']->created_at->diffForHumans() }}</p> <!-- Display time since message -->
                </div>
            </a>
        </div>
    @empty
        <div class="bg-white p-4 rounded-lg shadow-md mb-4 text-center">
            <p class="text-gray-500 text-lg">You have no friends to message.</p>
        </div>
    @endforelse
</div>
@endsection
