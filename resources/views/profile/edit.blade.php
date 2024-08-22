<!-- resources/views/profile/edit.blade.php -->

<form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <!-- Cover Photo -->
    <div>
        <label for="cover_photo">Cover Photo:</label>
        <input type="file" id="cover_photo" name="cover_photo">
    </div>

    <!-- Profile Picture -->
    <div>
        <label for="profile_picture">Profile Picture:</label>
        <input type="file" id="profile_picture" name="profile_picture">
    </div>

    <button type="submit">Update Profile</button>
</form>
