@extends('layouts.app')

@section('content')
<div class="w-full flex flex-wrap min-h-screen bg-gray-900 text-white mt-16 px-4 sm:px-6 lg:px-12">
    <!-- Profile Header Section -->
    <div class="w-full bg-gray-800 p-6 rounded-lg mb-6 flex items-center flex-col sm:flex-row">
        <img src="{{ asset('uploads/' . $user->profile) }}" alt="Profile Photo" class="w-20 h-20 rounded-full mb-4 sm:mb-0 sm:mr-4">
        <div class="text-center sm:text-left">
            <h1 class="text-2xl font-bold">{{ $user->name }}</h1>
            <p class="text-gray-400">{{ $user->email }}</p>
        </div>
        @if (Auth::check() && Auth::id() !== $user->id)
        <div class="w-full sm:w-auto mt-4 sm:mt-0 sm:ml-auto">
            @if ($friendRequestStatus === 'pending')
                <div class="flex space-x-4">
                    <form action="{{ route('friend.confirm', $user->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">Confirm</button>
                    </form>
                    <form action="{{ route('friend.delete', $user->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">Delete</button>
                    </form>
                </div>
            @elseif ($friendRequestStatus === 'sent')
                <span class="bg-yellow-600 px-4 py-2 rounded-lg">Request Sent</span>
            @elseif ($friendRequestStatus === 'friends')
                <span class="bg-gray-600 px-4 py-2 rounded-lg">Friends</span>
            @else
                <form action="{{ route('friend.add', $user->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 w-full sm:w-auto">Add Friend</button>
                </form>
            @endif
        </div>
    @endif

    </div>

    <!-- Posts Section -->
    <div class="mt-6 px-2 sm:px-6 lg:px-12">
        @if ($posts->isEmpty())
            <h2 class="text-xl font-semibold mb-3">No available posts.</h2>
        @else
            <h2 class="text-xl font-semibold mb-3">Available Posts</h2>
            @foreach ($posts as $post)
                <div class="bg-gray-800 p-6 rounded-lg mb-4">
                    <!-- Post Author and Timestamp -->
                    <ul class="mb-4">
                        <li class="flex items-center mb-4">
                            <a href="{{ route('profile.show', $post->user->id) }}" class="flex items-center">
                                <img src="{{ asset('uploads/' . $post->user->profile) }}" alt="User Photo" class="w-10 h-10 rounded-full mr-2">
                                <div>
                                    <h2 class="text-gray-400">{{ $post->user->name }}</h2>
                                    <span class="text-gray-500 block">{{ $post->created_at->format('F j, Y, g:i a') }}</span>
                                </div>
                            </a>
                            @if ($post->user_id == Auth::id())
                                <div class="ml-auto relative">
                                    <button class="text-gray-400 hover:text-white focus:outline-none" onclick="toggleMenu({{ $post->id }})">
                                        <i class="fas fa-ellipsis-h"></i>
                                    </button>
                                    <div id="menu-{{ $post->id }}" class="absolute right-0 mt-2 w-48 bg-gray-800 rounded-lg shadow-lg hidden z-10">
                                        <button onclick="openEditModal({{ $post->id }})" class="block px-4 py-2 text-sm text-gray-200 hover:bg-gray-700 w-full text-left">Edit Post</button>
                                        <form action="{{ route('posts.destroy', $post->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this post?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-200 hover:bg-gray-700">Delete Post</button>
                                        </form>
                                    </div>
                                </div>
                                @elseif ($post->user_id != Auth::id())
                                <div class="ml-auto">
                                    <button class="text-gray-400 hover:text-white focus:outline-none" onclick="toggleMenu({{ $post->id }})">
                                        <i class="fas fa-ellipsis-h"></i>
                                    </button>
                                    <div id="menu-{{ $post->id }}" class="absolute right-0 mt-2 w-48 bg-gray-800 rounded-lg shadow-lg hidden z-10">
                                        <button onclick="openReportModal({{ $post->id }})" class="block px-4 py-2 text-sm text-gray-200 hover:bg-gray-700 w-full text-left">Report Post</button>
                                    </div>
                                </div>
                            @endif
                        </li>
                    </ul>

                    <!-- Post Content and Media -->
                    <p class="text-white mb-4">{{ $post->content }}</p>
                    @if (Str::contains($post->media_path, ['.jpg', '.jpeg', '.png', '.gif', '.svg']))
                        <img src="{{ asset('storage/' . str_replace('public/', '', $post->media_path)) }}" alt="Post Media" class="object-cover rounded-lg mb-4">
                    @elseif (Str::contains($post->media_path, ['.mp4', '.mov', '.avi']))
                        <video controls class="object-cover rounded-lg mb-4">
                            <source src="{{ asset('storage/' . str_replace('public/', '', $post->media_path)) }}" type="video/{{ pathinfo($post->media_path, PATHINFO_EXTENSION) }}">
                            Your browser does not support the video tag.
                        </video>
                    @endif

                    <!-- Like, Comment, Share Buttons -->
                    <div class="flex flex-col space-y-2 mb-4">
                        <div class="flex space-x-8 items-center justify-around">
                            <!-- Like Button -->
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

                            <!-- Comment Button -->
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

                        <!-- Display Comments -->
                        @foreach ($post->comments as $comment)
                            <div class="bg-gray-700 p-4 rounded-lg mb-4">
                                <div class="flex items-center mb-2">
                                    <img src="{{ asset('uploads/' . $comment->user->profile) }}" alt="User" class="w-8 h-8 rounded-full mr-2">
                                    <p class="font-semibold">{{ $comment->user->name }}</p>
                                </div>
                                <p>{{ $comment->content }}</p>

                                <!-- Display Replies -->
                                @forelse ($comment->replies as $reply)
                                    <div class="mt-2 p-4 bg-gray-600 rounded-lg">
                                        <div class="flex items-center mb-2">
                                            <img src="{{ asset('uploads/' . $reply->user->profile) }}" alt="User" class="w-8 h-8 rounded-full mr-2">
                                            <p class="font-semibold">{{ $reply->user->name }}</p>
                                        </div>
                                        <p>{{ $reply->content }}</p>
                                    </div>
                                @empty
                                    <p class="text-gray-400 text-sm">No replies yet.</p>
                                @endforelse

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
        @endif
    </div>





</div>

<!-- Edit Modal -->
@foreach ($posts as $post)
<div id="edit-modal-{{ $post->id }}" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-70 hidden">
    <div class="bg-gray-800 p-6 rounded-lg w-full max-w-lg">
        <h3 class="text-lg font-semibold mb-4">Edit Post</h3>
        <form action="{{ route('posts.update', $post->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            <!-- Post Content -->
            <div class="mb-4">
                <label for="post_content" class="block text-gray-300">Post Content</label>
                <textarea id="post_content" name="post_content" rows="4" class="w-full px-4 py-2 border border-gray-600 rounded-lg bg-gray-900 text-white placeholder-gray-400">{{ old('post_content', $post->content) }}</textarea>
            </div>

            <!-- Media Upload -->
            <div class="mb-4">
                <label for="media" class="block text-gray-300">Media (optional)</label>
                <input id="media" name="media" type="file" class="w-full px-4 py-2 border border-gray-600 rounded-lg bg-gray-900 text-white placeholder-gray-400">
            </div>

            <!-- Submit Button -->
            <div class="mb-4">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Update Post</button>
                <button type="button" onclick="closeEditModal({{ $post->id }})" class="ml-4 bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">Cancel</button>
            </div>
        </form>
    </div>
</div>
@endforeach

<script>
    function openEditModal(postId) {
        document.getElementById('edit-modal-' + postId).classList.remove('hidden');
    }

    function closeEditModal(postId) {
        document.getElementById('edit-modal-' + postId).classList.add('hidden');
    }

    function toggleComments(postId) {
    const commentsSection = document.getElementById(`comments-${postId}`);
    if (commentsSection) {
        commentsSection.classList.toggle('hidden');
    }
}

function toggleReplyForm(postId, commentId) {
    const replyForm = document.getElementById(`reply-form-${postId}-${commentId}`);
    if (replyForm) {
        replyForm.classList.toggle('hidden');
    }
}

function toggleMenu(postId) {
    const menu = document.getElementById(`menu-${postId}`);
    if (menu) {
        menu.classList.toggle('hidden');
    }
}


    document.addEventListener('click', function(event) {
        const menus = document.querySelectorAll('[id^="menu-"]');
        menus.forEach(menu => {
            if (!menu.contains(event.target) && !event.target.closest('[onclick^="toggleMenu"]')) {
                menu.classList.add('hidden');
            }
        });
    });
</script>

@endsection
