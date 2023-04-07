<!doctype html>
<?php 
ob_start();
session_set_cookie_params(2592000);
session_start();
require 'core/header.php';
require 'core/classes/videos.php';
require 'core/classes/video.php';
$video = new Video($con, $_GET['v']);
if(empty($video->getTitle())) {
$videoExists = false;
} else {
$videoExists = true;
}

if($video->getCreatorRaw() !== $cUser->getID()) {
 $videoExists = false;
 die(header("Location: /watch?v=".$_GET['v']));
}
// ALWAYS ADD DIE WHEN YOU ARE REDIRECTING!!! reason: when no login u can still submit a video 
if(!$loggedIn) {
  die(header("Location: /login"));
}

$error = null;
if(isset($_POST['delete'])) {
$video->deleteVideo();
die(header("Location: /?success=Deleted%20video."));
}
if(isset($_POST['submit'])) {
if(empty($_POST['title']) || strlen($_POST['title']) < 0) {
  $error .= "Title field is empty. ";
 } elseif(strlen($_POST['privacy']) < 0) {
  $error .= "Privacy field is empty. ";
 }

if($_FILES['thumbnail']['name'] !== "" && empty($error)) {
  $fileName = $_FILES['thumbnail']['name'];
  $fileTmpName = $_FILES['thumbnail']['tmp_name'];
  
$thumbnail = "data:image/png;base64,".base64_encode(file_get_contents(__DIR__.$fileTmpName));
}
if(empty($thumbnail)) {
$thumbnail = $video->getThumbnailBase64();
}
$title = $_POST['title'];
if(empty($_POST['desc']) || strlen($_POST['desc']) < 0) {
$description = NULL;
} else {
$description = $_POST['desc'];
}
$privacy = (int)$_POST['privacy'];

$watchid = $video->getWatchID();
$query = $con->prepare('UPDATE videos SET title=:title, description=:description, thumbnail=:thumbnail, privacy=:privacy WHERE watch=:watchid');
$query->bindParam(':title', $title);
$query->bindParam(':description', $description);
$query->bindParam(':thumbnail', $thumbnail);
$query->bindParam(':privacy', $privacy);
$query->bindParam(':watchid', $watchid);
$query->execute();

die(header("Location: /watch?v=".$video->getWatchID()));
}
?>
<html lang="en" data-bs-theme="dark">
<head>
<?php echo embed(); ?>
<link
  rel="stylesheet"
  href="https://cdn.jsdelivr.net/npm/@vime/core@^5/themes/default.css"
/>
<script
  type="module" rel="preload"
  src="https://cdn.jsdelivr.net/npm/@vime/core@^5/dist/vime/vime.esm.js"
></script>
  <?php echo head(); ?>
   <title><?php echo $sitename." | ".$sitename; ?></title>
</head>
<body>
<?php echo navbar(); ?>
  <main class="container-fluid mt-3">
  <?php if(isset($error)) { ?>
   <div class="alert alert-danger d-flex"><i class="bi bi-exclamation-lg fs-4"></i> <span class="my-auto"><?php echo $error; ?></span></div>
   <?php } ?>
    <div class="card shadow-sm">
      <div class="card-header">Edit video</div>
      <div class="card-body d-flex flex-column flex-lg-row gap-2">
         <vm-player class="flex-grow-1" style="--vm-player-theme: #0078fa">
          <vm-video cross-origin="true" poster="<?php echo $video->getThumbnail(); ?>">
            <source data-src="<?php echo $video->getVideo(); ?>" type="video/mp4" />
          </vm-video>
          <vm-default-ui no-controls>
            <vm-default-controls hide-on-mouse-leave active-duration="2000">
            <vm-pip-control keys="p" />
            </vm-default-controls>
            <vm-default-settings pin="bottomRight">
              <vm-menu-item label="TestTube Player (Powered by Vime)"></vm-menu-item>
            </vm-settings>
          </vm-default-ui>
         </vm-player>
        <div class="card shadow-sm p-3 align-self-stretch align-self-lg-baseline">
        <form method="POST" enctype="multipart/form-data">
     <p>Sorry! For now you will not be able to change video files.</p>
     <div class="input-group mb-2">
      <input type="file" class="form-control" id="thumbnail-upload" name="thumbnail" accept="image/*">
      <label class="input-group-text" for="thumbnail-upload">Thumbnail</label>
     </div>
     <div class="input-group mb-2">
      <span class="input-group-text">Title</span>
      <input type="text" class="form-control" placeholder="Cool title" name="title" <?php if(!empty($video->getTitle())) { echo 'value="'.$video->getTitle().'"'; } ?>>
     </div>
     <div class="input-group mb-2">
      <span class="input-group-text">Description</span>
      <textarea class="form-control" placeholder="Awesome description" name="desc"><?php if(!empty($video->getDescription())) { echo $video->getDescription(); } ?></textarea>
     </div>
     <div class="input-group mb-1">
       <select class="btn btn-secondary dropdown-toggle" name="privacy">
        <option class="dropdown-item" value=0 <?php if($video->getPrivacy() == 0) { echo 'selected'; } ?>>Public</option>
        <option class="dropdown-item" value=1 <?php if($video->getPrivacy() == 1) { echo 'selected'; } ?>>Unlisted</option>
        <option class="dropdown-item" value=2 <?php if($video->getPrivacy() == 2) { echo 'selected'; } ?>>Private</option>
       </select>
     </div>
     <div class="hstack w-100">
     <input class="btn btn-success mt-2" type="submit" id="video-upload" name="submit" value="Update">
    </form>
    <form class="ms-auto" method="POST"><input class="btn btn-danger mt-2" type="submit" name="delete" value="Delete"></form>
    </div>
        </div>
      </div>
    </div>
  </main>
</body>
</html>