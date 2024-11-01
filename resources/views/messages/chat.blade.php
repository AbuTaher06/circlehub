@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8 px-4">
    <div class="bg-white p-6 rounded-lg shadow-lg mb-6">
        <!-- Chat header with user profile -->
        <a href="{{ route('profile.show', $chatUser->id) }}">
        <div class="flex items-center mb-4 border-b pb-4">
            <img src="{{ asset('uploads/'. $chatUser->profile) }}" alt="{{ $chatUser->name }}" class="w-12 h-12 rounded-full mr-4 border-2 border-blue-500">
            <h2 class="text-3xl font-bold text-gray-800">{{ $chatUser->name }}</h2>
        </div>
    </a>

        <!-- Message history -->
        <div id="message-list" class="mb-4 max-h-80 overflow-y-scroll p-4 border rounded-lg bg-gray-50">
            @foreach($messages as $message)
                <div class="flex {{ $message->sender_id == Auth::id() ? 'justify-end' : 'justify-start' }} mb-2">
                    @if ($message->sender_id != Auth::id())
                        <!-- Display the sender's profile image -->
                        <a href="{{ route('profile.show', $message->sender->id)}}"><img src="{{ asset('uploads/'. $message->sender->profile )}}" alt="{{ $message->sender->name }}" class="w-8 h-8 rounded-full mr-2 border-2 border-blue-400"></a>
                    @endif

                    <div class="bg-blue-100 p-3 rounded-lg shadow-sm max-w-xs">
                        <p class="text-gray-800">{{ $message->message }}</p>
                        <small class="text-gray-500">{{ $message->created_at->format('g:i A') }}</small>
                    </div>

                    @if ($message->sender_id == Auth::id())
                        <!-- Display the user's profile image -->
                        <a href="{{ route('profile.show', Auth::user()->id)}}"><img src="{{ asset('uploads/'.Auth::user()->profile )}}" alt="{{ Auth::user()->name }}" class="w-8 h-8 rounded-full ml-2 border-2 border-blue-500"></a>
                    @endif
                </div>
            @endforeach
        </div>

        <!-- Message input form -->
        <form id="message-form" action="{{ route('messages.send') }}" method="POST" class="flex mt-4">
            @csrf
            <!-- Hidden input for receiver ID -->
            <input type="hidden" name="receiver_id" value="{{ $chatUser->id }}">
            <!-- Input field for typing a message -->
            <input type="text" name="message" id="message-input" class="flex-1 border border-gray-300 p-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Type your message..." required>
            <!-- Submit button to send the message -->
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg ml-2 hover:bg-blue-700 transition duration-200 ease-in-out">
                Send
            </button>
        </form>
    </div>
</div>
@endsection
