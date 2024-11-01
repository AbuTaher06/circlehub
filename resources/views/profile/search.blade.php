@extends('layouts.app')

@section('content')
<div class="container mx-auto">
    <h1 class="text-2xl font-bold mb-4">Search Results for "{{ $query }}"</h1>
    @if($users->isEmpty())
        <p>No users found for "{{ $query }}".</p>
    @else
        <ul class="list-none">
            @foreach($users as $user)
            <a href="{{ route('profile.show', $user->id) }}" class="text-blue-500 hover:underline">
                <li class="flex items-center mb-4 p-4 border border-gray-200 rounded-lg">
                    <!-- Profile Picture -->
                    <img src="{{ asset($user->profile) ? asset('uploads/' . $user->profile) : 'https://via.placeholder.com/50' }}" alt="{{ $user->name }}" class="w-12 h-12 rounded-full mr-4">

                    <!-- User Info -->

                        <div class="font-semibold">{{ $user->name }}</div>
                        {{-- <div class="text-gray-600">{{ $user->email }}</div> --}}

                </li>
                
            </a>
            @endforeach
        </ul>
    @endif
</div>
@endsection
