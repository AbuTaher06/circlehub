<!-- resources/views/profile/edit-cover.blade.php -->

@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold mb-4">Edit Cover Photo</h1>

    <!-- Cover Photo Form -->
    <form action="{{ route('profile.update.cover', $user->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PATCH')

        <!-- Cover Photo Upload -->
        <div class="mb-4">
            <label for="cover_photo" class="block text-gray-700">New Cover Photo</label>
            <input id="cover_photo" name="cover_photo" type="file" class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100">
        </div>

        <!-- Submit Button -->
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Update Cover Photo</button>
    </form>
</div>
@endsection
