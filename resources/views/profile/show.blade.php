@extends('layouts.app')

@section('content')
<div class="w-full flex flex-wrap min-h-screen bg-gray-900 text-white mt-16 px-4 sm:px-6 lg:px-12">
    <!-- Profile Header Section -->
    <div class="w-full bg-gray-800 p-6 rounded-lg mb-6 flex flex-col items-center">
        <!-- Cover Photo -->
        <div class="relative w-full -mb-6">
            @if ($user->cover_photo)
                <img src="{{ asset('uploads/'. $user->cover_photo) }}" alt="Cover Photo" class="w-full h-48 object-cover rounded-lg">
            @else
                <img src="{{ asset('default-cover.jpg') }}" alt="Default Cover Photo" class="w-full h-48 object-cover rounded-lg">
            @endif
            @if (Auth::check() && Auth::id() === $user->id)
                <a href="{{ route('profile.edit.cover') }}" class="absolute top-4 right-4 bg-blue-600 text-white px-2 py-1 rounded-full hover:bg-blue-700">
                    <i class="fas fa-camera"></i>
                </a>
            @endif
        </div>

        <!-- Profile Picture and Edit Button -->
        <div class="relative mb-2">
            <img src="{{ asset('uploads/' . $user->profile) }}" alt="Profile Photo" class="w-32 h-32 rounded-full border-4 border-gray-800 shadow-md">
            @if (Auth::check() && Auth::id() === $user->id)
                <a href="{{ route('profile.edit') }}" class="absolute bottom-0 right-0 bg-blue-600 text-white px-2 py-1 rounded-full hover:bg-blue-700">
                    <i class="fas fa-edit"></i>
                </a>
            @endif
        </div>

        <!-- User Bio -->
<!-- Modal Structure -->
<div id="editBioModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex justify-center items-center hidden">
    <div class="bg-white p-6 rounded-lg w-96">
        <h2 class="text-xl font-bold mb-4 text-black">Edit Bio</h2>
        <form action="{{ route('profile.updateBio', ['id' => $user->id]) }}" method="POST">
            @csrf
            @method('PUT')
            <textarea name="bio" class="border border-gray-300 rounded-lg text-black w-full px-4 py-2">{{ old('bio', $user->bio) }}</textarea>
            <div class="mt-4 flex justify-end">
                <button type="button" id="closeModal" class="bg-gray-500 text-white px-4 py-2 rounded-lg mr-2 hover:bg-gray-600">
                    Cancel
                </button>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                    Save Bio
                </button>
            </div>
        </form>

    </div>
</div>

<div class="text-center">
    <h1 class="text-2xl font-bold">{{ $user->name }}</h1>
    <p class="text-gray-400 mb-2">{{ $user->bio ?: 'Life is so short. Enjoy Your Life.' }}</p>
    <button id="openModal" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 inline-block">
        Edit Bio
    </button>
</div>



        @if (Auth::check() && Auth::id() !== $user->id)
        <!-- Friend Request Status -->
        <div class="w-full mt-4">
            @if ($friendRequestStatus === 'pending')
                <div class="flex justify-center space-x-4">
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
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 w-full">Add Friend</button>
                </form>
            @endif
        </div>
        @endif

        @auth
        <!-- Activity Feed and Privacy Settings Buttons -->
        <div class="w-full mt-4 flex justify-between items-center">
            <!-- Activity Feed Button -->
            <button onclick="toggleActivityFeed()" id="activity-feed-button" class="flex items-center bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                <i class="fas fa-bell mr-2"></i>
                <span>Activity Feed</span>
            </button>

            <a href="{{ route('messages.get', ['userId' => $user->id]) }}" class="flex items-center space-x-2 btn bg-green-600 text-white px-4 py-2 rounded-lg hover:text-gray-300">
                {{-- <i class="fas fa-comment-dots"></i> <!-- Messenger icon --> --}}
                <span>Send Message</span>
            </a>


            <!-- Privacy Settings Button -->
            @if (Auth::check() && Auth::id() === $user->id)
            <button onclick="openPrivacyModal()" class="flex items-center bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
                <i class="fas fa-lock mr-2"></i>
                <span>Privacy Settings</span>
            </button>
            @endif
        </div>

        <!-- Activity Feed -->
        <div id="activity-feed" class="w-full mt-2 hidden">
            @if(Auth::check() && Auth::id() === $user->id)
                @php
                    $activities = Auth::user()->activities()->latest()->get();
                @endphp
                @forelse ($activities as $activity)
                    <div class="activity bg-gray-700 p-4 rounded-lg mb-2">
                        @php
                            $actorName = $activity->user->id === auth()->id() ? 'You' : $activity->user->name;
                            $postUrl = route('posts.show', ['id' => $activity->post_id]);
                            $profileUrl = route('profile.show', ['id' => $activity->user_id]);
                        @endphp

                        @if ($activity->type == 'like')
                            <p>
                                <a href="{{ $postUrl }}" class="text-blue-400">{{ $actorName }} liked your post</a>
                                - {{ $activity->created_at->diffForHumans() }}
                            </p>
                        @elseif ($activity->type == 'comment')
                            <p>
                                <a href="{{ $postUrl }}" class="text-blue-400">{{ $actorName }} commented on a post</a>
                                - {{ $activity->created_at->diffForHumans() }}
                            </p>
                        @elseif ($activity->type == 'visit')
                            <p>
                                <a href="{{ $profileUrl }}" class="text-blue-400">{{ $actorName }} visited your profile</a>
                                - {{ $activity->created_at->diffForHumans() }}
                            </p>
                        @elseif ($activity->type == 'post')
                            <p>
                                <a href="{{ $postUrl }}" class="text-blue-400">{{ $actorName }} created a new post</a>
                                - {{ $activity->created_at->diffForHumans() }}
                            </p>
                        @endif
                    </div>
                @empty
                    <p class="text-gray-400">No recent activities.</p>
                @endforelse
            @else
                <p class="text-gray-400">You can only view your own activities.</p>
            @endif
        </div>

        <!-- Privacy Settings Modal -->
        <div id="privacy-modal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden">
            <div class="bg-gray-800 p-6 rounded-lg w-80">
                <h2 class="text-xl font-semibold mb-4">Change Privacy Settings</h2>
                <form id="privacy-form" method="POST" action="{{ route('posts.update.privacy', 'post_id_placeholder') }}">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="post_id" id="privacy-post-id">
                    <div class="mb-4">
                        <label class="block text-gray-400 mb-2">Privacy Level:</label>
                        <select name="privacy" id="privacy-select" class="w-full px-4 py-2 border border-gray-600 rounded-lg bg-gray-900 text-white">
                            <option value="public">Public</option>
                            <option value="friends">Friends</option>
                            <option value="private">Private</option>
                        </select>
                    </div>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Save Changes</button>
                    <button type="button" onclick="closePrivacyModal()" class="ml-4 bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">Cancel</button>
                </form>
            </div>
        </div>
        @endauth
    </div>

    <!-- Posts Section -->
    <div class="w-full sm:w-2/3 mt-6 px-2 sm:px-6 lg:px-12">
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
                                <img src="{{ asset('uploads/'.$post->user->profile) }}" alt="User Photo" class="w-10 h-10 rounded-full mr-2">
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
                                    <!-- Menu for editing and deleting the post -->
                                    <div id="menu-{{ $post->id }}" class="absolute right-0 mt-2 w-48 bg-gray-800 rounded-lg shadow-lg hidden z-10">
                                        <button onclick="openEditModal({{ $post->id }})" class="block px-4 py-2 text-sm text-gray-200 hover:bg-gray-700 w-full text-left">Edit Post</button>
                                        <button onclick="openPrivacyModal({{ $post->id }})" class="block px-4 py-2 text-sm text-gray-200 hover:bg-gray-700 w-full text-left">Change Privacy</button>
                                        <form action="{{ route('posts.destroy', $post->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this post?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-200 hover:bg-gray-700">Delete Post</button>
                                        </form>
                                    </div>
                                </div>
                            @endif
                        </li>
                    </ul>

                    <!-- Privacy Label -->
                    <div class="mb-2">
                        <span class="text-gray-400 text-sm">
                            Privacy:
                            <strong class="{{ $post->privacy === 'public' ? 'text-green-500' : ($post->privacy === 'friends' ? 'text-yellow-500' : 'text-red-500') }}">
                                {{ ucfirst($post->privacy) }}
                            </strong>
                        </span>
                    </div>

                    <!-- Post Content and Media -->
                    <p class="text-white mb-4">{{ $post->content }}</p>
                    @if (Str::contains($post->media_path, ['.jpg', '.jpeg', '.png', '.gif', '.svg']))
                        <img src="{{ Storage::url($post->media_path) }}" alt="Post Media" class="object-cover rounded-lg mb-4">
                    @elseif (Str::contains($post->media_path, ['.mp4', '.mov', '.avi']))
                        <video controls class="object-cover rounded-lg mb-4">
                            <source src="{{ Storage::url($post->media_path) }}" type="video/{{ pathinfo($post->media_path, PATHINFO_EXTENSION) }}">
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
                                    <img src="{{ Storage::url($comment->user->profile) }}" alt="User" class="w-8 h-8 rounded-full mr-2">
                                    <p class="font-semibold">{{ $comment->user->name }}</p>
                                </div>
                                <p>{{ $comment->content }}</p>

                                <!-- Display Replies -->
                                @forelse ($comment->replies as $reply)
                                    <div class="mt-2 p-4 bg-gray-600 rounded-lg">
                                        <div class="flex items-center mb-2">
                                            <img src="{{ Storage::url($reply->user->profile) }}" alt="User" class="w-8 h-8 rounded-full mr-2">
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

    <!-- Friends Section -->
    <div class="w-full sm:w-1/3 bg-gray-800 p-6 rounded-lg mb-6">
        <h2 class="text-xl font-semibold text-gray-200">Friends</h2>
        <ul class="mt-4">
            @forelse ($mutualFriends as $friend)
                <li class="mb-2 flex items-center">
                    <img src="{{ asset('uploads/' . $friend->profile) }}" alt="{{ $friend->name }}" class="w-8 h-8 rounded-full mr-2">
                    <a href="{{ route('profile.show', $friend->id) }}" class="text-gray-300 hover:underline">{{ $friend->name }}</a>
                </li>
            @empty
                <li class="text-gray-400">No mutual friends found.</li>
            @endforelse
        </ul>

        <h2 class="text-xl font-semibold text-gray-200 mt-6">Recently Added Friends</h2>
        <ul class="mt-4">
            @forelse ($recentlyAddedFriends as $friend)
                <li class="mb-2 flex items-center">
                    <img src="{{ asset('uploads/' . $friend->profile) }}" alt="{{ $friend->name }}" class="w-8 h-8 rounded-full mr-2">
                    <a href="{{ route('profile.show', $friend->id) }}" class="text-gray-300 hover:underline">{{ $friend->name }}</a>
                    <span class="text-gray-500 text-sm ml-auto">{{ $friend->pivot->created_at->diffForHumans() }}</span>
                </li>
            @empty
                <li class="text-gray-400">No recently added friends.</li>
            @endforelse
        </ul>
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
    function toggleActivityFeed() {
        const activityFeed = document.getElementById('activity-feed');
        activityFeed.classList.toggle('hidden');
    }

    function openPrivacyModal(postId) {
    document.getElementById('privacy-post-id').value = postId;
    document.getElementById('privacy-form').action = "{{ route('posts.update.privacy', '') }}/" + postId;
    document.getElementById('privacy-modal').classList.remove('hidden');
}


    function closePrivacyModal() {
        document.getElementById('privacy-modal').classList.add('hidden');
    }

    document.getElementById('privacy-form').addEventListener('submit', function(event) {
    event.preventDefault(); // Prevent default form submission

    const form = event.target;
    const postId = form.querySelector('input[name="post_id"]').value;
    const privacy = form.querySelector('select[name="privacy"]').value;

    fetch(`/posts/${postId}/privacy`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ privacy: privacy })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            closePrivacyModal(); // Close modal on success
            location.reload(); // Refresh the page to see the updated privacy settings
        } else {
            alert('Failed to update privacy settings');
        }
    })
    .catch(error => console.error('Error:', error));
});



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

    // Open modals when clicking on the "Edit" button for bio
    document.getElementById('openModal').addEventListener('click', function() {
        document.getElementById('editBioModal').classList.remove('hidden');
    });

    document.getElementById('closeModal').addEventListener('click', function() {
        document.getElementById('editBioModal').classList.add('hidden');
    });
    document.getElementById('editBioModal').addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.add('hidden');
        }
    });

    //close privacy modal
    document.getElementById('privacy-modal').addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.add('hidden');
        }
    });




    // Close modals when clicking outside of them
    document.addEventListener('click', function(event) {
        const openModals = document.querySelectorAll('.modal:not(.hidden)');
        openModals.forEach(modal => {
            if (!modal.contains(event.target) && !event.target.closest('.modal-toggle')) {
                modal.classList.add('hidden');
            }
        });
    });
</script>

@endsection

