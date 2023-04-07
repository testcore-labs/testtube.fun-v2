<?php 
ob_start();
session_set_cookie_params(2592000);
session_start();
header("Content-Type: application/json");
require '../core/config.php';
require '../core/classes/user.php';
require '../core/classes/video.php';
$user = new User($con, $_POST['user']);

if(isset($_SESSION['user'])) {
$user = $user->addSub();
die(' {
    "response": '.$user['response'].',
    "value": "'.$user['value'].'"
  }');
} else {
die(' {
     "response": 2
  }');
}