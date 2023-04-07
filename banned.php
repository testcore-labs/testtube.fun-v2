<!doctype html>
<?php 
ob_start();
session_set_cookie_params(2592000);
session_start();
require 'core/header.php';
require_once 'core/classes/strikes.php';
if(!$loggedIn) {
 die(header("Location: /login"));
}
$strikes = New Strikes($con, $cUser->getID(), true);
$strike = New Strikes($con, $cUser->getID());
$isBanned = $strikes->getStrikeCount();
if(!($isBanned >= 3)) {
 die(header("Location: /"));
}
?>
<html lang="en" data-bs-theme="dark">
<head>
  <?php echo head(); ?>
  <title><?php echo $pagename." | ".$sitename; ?></title>
  <?php echo embed("Beaned"); ?>
</head>
<body>
<?php echo navbar(); ?>
  <main class="container mt-3">
    <?php if($isBanned >= 3) { ?>
     <div class="card">
      <div class="card-body">
       <h2 class="">Banned</h2>
       Sorry, but your account has been permanently banned due to multiple violations of our terms of service. After three strikes, we have determined that your actions were in direct violation of our policies, and we can no longer allow you to access our platform. We take these matters seriously and expect all users to adhere to our guidelines. 
       <div class="card mt-2 mb-2">
        <div class="lh-lg px-3 py-2 d-block overflow-scroll" style="max-height: 8rem;">
        Admin: <?php echo $strike->getAdmin(); ?><br>
        Note: <?php echo $strike->getNote(); ?>
        </div>
        </div>
        <i>You can still make a new account.</i><br>
        <a href="/logout" class="btn btn-primary mt-2">Logout</a>
      </div>
     </div>
    <?php } ?>
  </main>
</body>
</html>
