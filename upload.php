<!doctype html>
<?php
ob_start();
session_set_cookie_params(2592000);
session_start();
require 'core/header.php';
require 'core/classes/videos.php';
require 'core/classes/video.php';
require_once __DIR__.'/core/config.php';
require_once __DIR__.'/vendor/autoload.php';
/*
  _____         _  _____      _                        _                 _           
 |_   _|__  ___| ||_   _|   _| |__   ___   _   _ _ __ | | ___   __ _  __| | ___ _ __ 
   | |/ _ \/ __| __|| || | | | '_ \ / _ \ | | | | '_ \| |/ _ \ / _` |/ _` |/ _ \ '__|
   | |  __/\__ \ |_ | || |_| | |_) |  __/ | |_| | |_) | | (_) | (_| | (_| |  __/ |   
   |_|\___||___/\__||_| \__,_|_.__/ \___|  \__,_| .__/|_|\___/ \__,_|\__,_|\___|_|   
                                                |_|                               
  Idk why I added this but yeah lol.
  cope rights reserved to mr qzip and testtub   
*/

// ALWAYS ADD DIE WHEN YOU ARE REDIRECTING!!! reason: when no login u can still submit a video 
if(!$loggedIn) {
  die(header("Location: /login"));
}

$query = $con->prepare("SELECT date FROM videos WHERE creator=:id ORDER by DATE DESC");
$query->bindParam(":id", $_SESSION['user'], PDO::PARAM_INT);
$query->execute();
$timeago = $query->fetch(PDO::FETCH_ASSOC)['date'] ?? 0;

$timeago = time() - $timeago;
if($timeago < 300) {
  if(!$cUser->getIsVerified()) {
  $timeError = urlencode("Please wait 5 minutes before uploading.");
  die(header("Location: /?danger=".$timeError));
  }
}

$error = null;

// Define FFMpeg settings
$settings = Array(
  'ffmpeg.binaries'  => '/usr/bin/ffmpeg',
  'ffprobe.binaries'  => '/usr/bin/ffprobe',
  'timeout'          => 3600, // The timeout for the underlying process
  'ffmpeg.threads'   => 1,   // The number of threads that FFMpeg should use
);

if(isset($_POST['submit'])) {
if(empty($_POST['title']) || strlen($_POST['title']) < 0) {
  $error .= "Title field is empty. ";
 } elseif(strlen($_POST['privacy']) < 0) {
  $error .= "Privacy field is empty. ";
 } elseif(empty($_FILES['video']['name'])) {
  $error .= "Video field is empty. ";
 }
}

if(isset($_FILES['video']) && empty($error)) {
  $fileName = $_FILES['video']['name'];
  $fileTmpName = $_FILES['video']['tmp_name'];
  
  // Move uploaded file to target directory
  $targetDir = "uploads/vids/"; // Target directory to save files
  $targetFilePath = $targetDir . uniqid().".mp4"; // Concatenate target directory and file name
  if(move_uploaded_file($fileTmpName, $targetFilePath)) {
$inputFile = $targetFilePath;
if(file_exists($inputFile)) {
// Create a new FFMpeg instance
$ffmpeg = FFMpeg\FFMpeg::create($settings);
$vidmeta = FFMpeg\FFProbe::create($settings);
if (!$vidmeta->isValid($inputFile)) {
  $error .= "Invalid video. ";
  unlink($inputFile);
} else {
try {
// Open the input file with FFMpeg
$video = $ffmpeg->open($inputFile);
// Get the video metadata with FFProbe

// Check if the input file is valid

// Get the duration of the video
$duration = $vidmeta->format($inputFile)->get('duration');

// Apply filters to the video
$video
  ->filters()
  ->synchronize();

// Define the number of frames to extract
$frames = 1;
if($_FILES['thumbnail']['name'] == "") {
// Extract frames from the video
for ($i = 1; $i <= $frames; $i++) {
  $frame = ($duration * 0.3) / $frames * $i;
  $thumbnail = $video->frame(FFMpeg\Coordinate\TimeCode::fromSeconds($frame))
    ->save($i.'.png',false,true);
}

/*$image = new Imagick();
$image->readImageBlob($thumbnail);
$image->resizeImage(1280, 720, Imagick::FILTER_LANCZOS, 1);
$image->getImageBlob();*/
$thumbnail = "data:image/png;base64,".base64_encode($thumbnail);
} else {
  $fileName = $_FILES['thumbnail']['name'];
  $fileTmpName = $_FILES['thumbnail']['tmp_name'];
  
$thumbnail = "data:image/png;base64,".base64_encode(file_get_contents($fileTmpName));
}

// Generate a random ID for the video
function idGen() {
$rid = "";
$characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz-_';
for ($i = 0; $i < 24; $i++) {
    $rid .= $characters[mt_rand(0, 63)];
}
return $rid;
}
$rid = idGen();
$testVideo = new Video($con, $rid);
if($rid == $testVideo->getWatchID()) {
  $rid = idGen(); // start over...
}

$title = $_POST['title'];
if(empty($_POST['desc']) || strlen($_POST['desc']) < 0) {
$description = NULL;
} else {
$description = $_POST['desc'];
}
$privacy = (int)$_POST['privacy'];

$creator = $_SESSION['user'];
$inputFile = "/".$inputFile;
$time = time();

$hours = floor($duration / 3600);
$mins = floor(($duration - ($hours*3600)) / 60);
$secs = floor($duration % 60);


$hours = ($hours < 1) ? "" : $hours . ":";
$mins = ($mins < 10) ? "" . $mins . ":" : $mins . ":";
$secs = ($secs < 10) ? "0" . $secs : $secs;

$duration = $hours.$mins.$secs;

$query = $con->prepare('INSERT INTO videos (watch, title, description, creator, file, privacy, thumbnail, duration, date)
VALUES (:watch, :title, :description, :creator, :file, :privacy, :thumbnail, :duration, :time); ');
$query->bindParam(':watch', $rid);
$query->bindParam(':title', $title);
$query->bindParam(':description', $description);
$query->bindParam(':creator', $creator);
$query->bindParam(':file', $inputFile);
$query->bindParam(':thumbnail', $thumbnail);
$query->bindParam(':privacy', $privacy);
$query->bindParam(':duration', $duration);
$query->bindParam(':time', $time);
$query->execute();

$query = $con->prepare("SELECT watch FROM videos WHERE creator=:id ORDER by DATE DESC");
$query->bindParam(":id", $_SESSION['user'], PDO::PARAM_INT);
$query->execute();
$watchID = $query->fetch(PDO::FETCH_ASSOC)['watch'];
/*
header("Location: /watch?v=".$watchID);*/
} catch (Exception $e) {
  unlink(realpath($_SERVER["DOCUMENT_ROOT"]).$inputFile);
  echo 'Error occured!: ',  $e->getMessage(), "\n";
}
}
}
}
}
?>
<html lang="en" data-bs-theme="dark">
<head>
  <?php echo head(); ?>
  <title><?php echo $pagename." | ".$sitename; ?></title>
  <?php echo embed("You need to login to upload a video."); ?>
</head>
<body>
<?php echo navbar(); ?>
  <main class="container-fluid mt-3">
   <?php if(isset($error)) { ?>
   <div class="alert alert-danger d-flex"><i class="bi bi-exclamation-lg fs-4"></i> <span class="my-auto"><?php echo $error; ?></span></div>
   <?php } ?>
  <div class="d-flex flex-column flex-md-row">

  <div class="col card shadow-sm m-2 align-self-baseline">
    <div class="card-header h3">Upload</div>
    <div class="card-body">
    <form method="POST" enctype="multipart/form-data">
    <div class="input-group mb-2">
      <input type="file" class="form-control" id="video-upload" name="video" accept="video/*">
      <label class="input-group-text" for="video-upload">Video</label>
     </div>
     <div class="input-group mb-2">
      <input type="file" class="form-control" id="thumbnail-upload" name="thumbnail" accept="image/*">
      <label class="input-group-text" for="thumbnail-upload">Thumbnail</label>
     </div>
     <div class="input-group mb-2">
      <span class="input-group-text">Title</span>
      <input type="text" class="form-control" placeholder="Cool title" name="title">
     </div>
     <div class="input-group mb-2">
      <span class="input-group-text">Description</span>
      <textarea class="form-control" placeholder="Awesome description" name="desc"></textarea>
     </div>
     <div class="input-group mb-1">
       <select class="btn btn-secondary dropdown-toggle" name="privacy">
        <option class="dropdown-item" value=0>Public</option>
        <option class="dropdown-item" value=1>Unlisted</option>
        <option class="dropdown-item" value=2>Private</option>
       </select>
     </div>

     <i class="small">* Custom thumbnails can be uploaded later in the video editor.</i><br>
     <input class="btn btn-success mt-2" type="submit" id="video-upload" name="submit" value="Upload" data-bs-toggle="modal" data-bs-target="#upload">
    </form>
   </div>
  </div>
  <div class="modal fade" id="upload" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="exampleModalLabel">Uploading</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center">
       <div class="spinner-border text-primary fs-4 m-2" style="width: 3rem; height: 3rem;" role="status">
        <span class="visually-hidden">Uploading</span>
       </div><br>
        Uploading...<br>
      </div>
    </div>
  </div>
</div>

   
   
   <div class="col col-md-5 card shadow-sm m-2 align-self-baseline">
    <div class="card-header h3">Before uploading</div>
    <div class="card-body">
     Please read the <a href="/tos#rules" class="text-decoration-none">rules</a> before uploading <b>ANY</b> video.<br>If you do not follow the rules, we will have permission to take action based on the rules.
    </div>
   </div>
   
   </div>
  </main>
</body>
</html>
