@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Notifications</h2>
    <ul>
        @forelse ($notifications as $notification)
            <li class="{{ $notification->read_at ? '' : 'font-bold' }}">
                {{ $notification->data['message'] }}
                <small>{{ $notification->created_at->diffForHumans() }}</small>
                <!-- Optionally add a link to the related request or action -->
            </li>
        @empty
            <li>No notifications available.</li>
        @endforelse
    </ul>
</div>
@endsection
