<!doctype html>
<?php 
ob_start();
session_set_cookie_params(2592000);
session_start();
require 'core/header.php';
require 'core/classes/videos.php';
require 'core/classes/video.php';
if(empty($_GET['v'])) {
header("Location: /?danger=Invalid%20video%20ID");
}
$video = new Video($con, $_GET['v']);
if(empty($video->getTitle())) {
$videoExists = false;
} else {
$videoExists = true;
$video->addViews();
$user = new User($con, $video->getCreatorRaw());
}

if($video->getPrivacy() == 2) {
if($video->getCreatorRaw() !== $_SESSION['user']) {
 $videoExists = false; // init the fuckin lieszz
}
}

if($cUser->getIsAdmin()) {
if(isset($_POST['delete'])) {
  $video->deleteVideo();
  die(header("Location: /?success=Deleted%20video."));
}
}
?>
<html lang="en" data-bs-theme="dark">
<head>
<link
  rel="stylesheet"
  href="https://cdn.jsdelivr.net/npm/@vime/core@^5/themes/default.css"
/>
<script
  type="module" rel="preload"
  src="https://cdn.jsdelivr.net/npm/@vime/core@^5/dist/vime/vime.esm.js"
></script>
</head>
<body style="padding: 0; margin: 0; height: 100vh">
<?php if(!$videoExists) { ?>
?
<?php die(); }?>
        <vm-player style="--vm-player-theme: #0078fa; height: 100vh">
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
</body>
</html>