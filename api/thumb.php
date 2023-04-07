<?php
ob_start();
session_set_cookie_params(2592000);
session_start();
require '../core/config.php';
require '../core/classes/video.php';
if(isset($_REQUEST['v'])) {
$video = new Video($con, $_REQUEST['v']);
$image = base64_decode(str_replace("data:image/png;base64,", "", $video->getThumbnailBase64()));
header("Content-Type: image/png");
die($image);
}