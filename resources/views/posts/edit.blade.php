@extends('layouts.app')

@section('content')
<div class="w-full flex flex-wrap min-h-screen bg-gray-900 text-white mt-16 px-12">
    <div class="mt-6">
        <h2 class="text-xl font-semibold mb-3">Edit Post</h2>

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
            </div>
        </form>
    </div>
</div>
@endsection
