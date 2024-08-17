@extends('layouts.app')

@section('content')
<div class="w-full flex flex-wrap min-h-screen bg-gray-900 text-white mt-16 px-12">
      <div class="mt-6">
        <h2 class="text-xl font-semibold mb-3">Your Posts</h2>
    @foreach ($posts as $post)
    <div class="bg-gray-800 p-6 rounded-lg mb-4">

        <!-- Post Content and Media -->
        <ul>
            <li class="flex items-center mb-4">
                <img src="{{ asset('uploads/' . Auth::user()->profile) }}" alt="Friend" class="w-10 h-10 rounded-full mr-2">
                <div>
                    <h2 class="text-gray-400">{{ Auth::user()->name }}</h2>
                    <span class="text-gray-500 block">{{ $post->created_at->format('F j, Y, g:i a') }}</span>
                </div>
            </li>
        </ul>

        <p class="text-white mb-4">{{ $post->content }}</p>
        @if (Str::contains($post->media_path, ['.jpg', '.jpeg', '.png', '.gif', '.svg']))
            <img src="{{ asset('storage/' . str_replace('public/', '', $post->media_path)) }}"
                 alt="Post Media"
                 class="w-[250px] h-[200px] object-cover rounded-lg mb-4">
        @else
            @if (Str::contains($post->media_path, ['.mp4', '.mov', '.avi']))
                <video controls
                       class="w-[250px] h-[200px] object-cover rounded-lg mb-4">
                    <source src="{{ asset('storage/' . str_replace('public/', '', $post->media_path)) }}"
                            type="video/{{ pathinfo($post->media_path, PATHINFO_EXTENSION) }}">
                    Your browser does not support the video tag.
                </video>
            @endif
        @endif

        <!-- Like, Comment, Share Buttons with Count Above -->
        <div class="flex flex-col space-y-2 mb-4">
            <div class="flex space-x-8 items-center justify-around">
                <!-- Like Count and Button -->
                <div class="text-center">
                    <span class="text-sm text-gray-400 block">{{ $post->likes->count() }}</span>
                    <form action="{{ route('likes.toggle', $post->id) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="bg-gray-600 px-4 py-2 rounded-lg hover:bg-gray-700 flex items-center space-x-2">
                            @if ($post->likes->where('user_id', Auth::id())->count() > 0)
                                <i class="fas fa-thumbs-up"></i>
                                <span class="hidden md:inline">Liked</span>
                            @else
                                <i class="fas fa-thumbs-up"></i>
                                <span class="hidden md:inline">Like</span>
                            @endif
                        </button>
                    </form>
                </div>

                <!-- Comment Count and Button -->
                <div class="text-center">
                    <span class="text-sm text-gray-400 block">{{ $post->comments->count() }}</span>
                    <button class="bg-gray-600 px-4 py-2 rounded-lg hover:bg-gray-700 flex items-center space-x-2" onclick="toggleComments({{ $post->id }})">
                        <i class="fas fa-comment-dots"></i>
                        <span class="hidden md:inline">Comment</span>
                    </button>
                </div>

                <!-- Share Button -->
                <div class="text-center">
                    <span class="text-sm text-gray-400 block">{{ $post->shares->count() }}</span>
                    <form action="{{ route('shares.store', $post->id) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="bg-gray-600 px-4 py-2 rounded-lg hover:bg-gray-700 flex items-center space-x-2">
                            <i class="fas fa-share"></i>
                            <span class="hidden md:inline">Share</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Comment Section -->
        <div id="comments-{{ $post->id }}" class="mt-4 hidden">
            <!-- Comment Form -->
            <div class="mb-4">
                <form action="{{ route('comments.store', $post->id) }}" method="POST">
                    @csrf
                    <textarea name="comment_content" rows="3" class="w-full px-4 py-2 border border-gray-600 rounded-lg bg-gray-900 text-white placeholder-gray-400" placeholder="Write a comment..."></textarea>
                    <input type="hidden" name="parent_id" id="parent-id-{{ $post->id }}">
                    <button type="submit" class="mt-2 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Post Comment</button>
                </form>
            </div>

            <!-- Display Comments and Replies -->
            @foreach ($post->comments->where('parent_id', null) as $comment)
                <div class="mt-4 p-4 bg-gray-700 rounded-lg">
                    <div class="flex items-center mb-2">
                        <img src="{{ asset('uploads/' . $comment->user->profile) }}" alt="User" class="w-8 h-8 rounded-full mr-2">
                        <p class="font-semibold">{{ $comment->user->name }}</p>
                    </div>
                    <p>{{ $comment->content }}</p>

                    <!-- Display Replies -->
                    @foreach ($comment->replies as $reply)
                        <div class="mt-2 p-4 bg-gray-600 rounded-lg">
                            <div class="flex items-center mb-2">
                                <img src="{{ asset('uploads/' . $reply->user->profile) }}" alt="User" class="w-8 h-8 rounded-full mr-2">
                                <p class="font-semibold">{{ $reply->user->name }}</p>
                            </div>
                            <p>{{ $reply->content }}</p>
                        </div>
                    @endforeach

                    <!-- Reply Form -->
                    <div class="mt-2">
                        <button class="bg-gray-600 px-4 py-2 rounded-lg hover:bg-gray-700 flex items-center space-x-2" onclick="toggleReplyForm({{ $post->id }}, {{ $comment->id }})">
                            <i class="fas fa-reply"></i>
                            <span class="hidden md:inline">Reply</span>
                        </button>
                        <div id="reply-form-{{ $post->id }}-{{ $comment->id }}" class="mt-2 hidden">
                            <form action="{{ route('comments.store', $post->id) }}" method="POST">
                                @csrf
                                <textarea name="comment_content" rows="3" class="w-full px-4 py-2 border border-gray-600 rounded-lg bg-gray-900 text-white placeholder-gray-400" placeholder="Write a reply..."></textarea>
                                <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                                <button type="submit" class="mt-2 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Post Reply</button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endforeach
</div>
</div>

<script>
    function toggleComments(postId) {
        const commentsSection = document.getElementById('comments-' + postId);
        commentsSection.classList.toggle('hidden');
    }

    function toggleReplyForm(postId, commentId) {
        const form = document.getElementById('reply-form-' + postId + '-' + commentId);
        form.classList.toggle('hidden');
    }
</script>

@endsection
