<form action="api/profile/update-photo" method="POST" enctype="multipart/form-data">
    @method('PUT')
    <input type="text" name="user_id"> <br>
    <input type="file" name="photo_profile"> <br>
    <input type="submit" value="Submit">
</form>