<!doctype html>
<?php
ob_start();
session_set_cookie_params(2592000);
session_start();
require 'core/header.php';
require_once 'core/classes/user.php';
require 'core/classes/video.php';
$sql = $con->prepare("SELECT id FROM users WHERE username=:un");
$sql->bindParam(":un", $_GET['id']);
$sql->execute();
$id = $sql->fetch(PDO::FETCH_ASSOC)['id'] ?? 0;
$user = new User($con, $id);
if(empty($user->getID())) {
    $userExists = false;
    } else {
    $userExists = true;
    }
?>
<html lang="en" data-bs-theme="dark">
<head>
<meta http-equiv="Content-Security-Policy" content="default-src 'self' <?php echo $_SERVER['HTTP_HOST']; ?> data: cdn.jsdelivr.net code.jsdelivr.net mdbootstrap.com *.jsdelivr.net *.w3.org *.discordapp.net *.discordapp.com wsrv.nl; style-src 'self' 'unsafe-inline' cdn.jsdelivr.net code.jsdelivr.net mdbootstrap.com *.jsdelivr.net *.w3.org *.discordapp.net *.discordapp.com wsrv.nl; script-src 'self' <?php echo $_SERVER['HTTP_HOST']; ?> wsrv.nl * cdn.jsdelivr.net code.jsdelivr.net mdbootstrap.com *.jsdelivr.net 'unsafe-inline' cdn.jsdelivr.net code.jsdelivr.net mdbootstrap.com *.jsdelivr.net wsrv.nl *;">
  <?php echo head(); ?>
  <title><?php if(!$userExists) { echo "?"; } else { echo $user->getUsername(false); } echo " | ".$sitename; ?></title>
  <?php if(!$userExists) { ?>
<meta property="og:url" content="//<?php echo $_SERVER['HTTP_HOST']; ?>">
<meta property="og:title" content="User not found" />
<meta property="og:image" itemprop="image" content="/assets/img/icon.png">
<meta property="og:site_name" content="<?php echo $sitename; ?>">
<?php } else { ?>
<meta property="og:site_name" content="<?php echo $sitename; ?>">
<meta property="og:image" content="https://<?php echo $_SERVER["HTTP_HOST"].$user->getAvatar(); ?>"/>
<meta content="<?php echo $user->getUsername(false); ?>" property="og:title">
<meta content="<?php echo $user->getBio(); ?>" property="og:description">
<?php } ?>
<meta name="theme-color" content="#0078fa">
  <?php echo $user->getCustom(); ?>

<style type="text/css">
.admin {
display: block;
}
</style>
</head>
<body>
<?php echo navbar(); ?>
<main class="container-fluid mt-3">

<?php if(!$userExists) { ?>
<div class="rounded-2 border border-1 p-3 bg-body w-100" style="min-height: 56.25% height: 56.25%">
<span class="h2">User not found</span>
<hr>
<span>We're not sure if the user has been deleted, banned or just straight up didn't exist.</span>
</div>
<?php die(); }?>
<div class="card shadow-sm card-1">
 <div class="ratio" style="--bs-aspect-ratio: 20%;">
  <img class="bg-body-tertiary rounded-top-1 banner" src="<?php echo $user->getBanner(); ?>" alt="">
 </div>
 <div class="card-body">
  <div class="d-flex flex-column flex-sm-row">
   <img class="rounded-circle float-none float-sm-start me-3 avatar" width=128 height=128 src="<?php echo $user->getAvatar(); ?>" alt="<?php echo $user->getUsername(false); ?>">
   <span class="h2 my-auto username"><?php echo $user->getUsername(); ?>
   <p class="text-muted h6 h5-sm mt-2 subcount" id="subcount"><?php echo $user->getSubs(true); ?> subscribers</p></span>
   <a class="btn btn-danger h-25 ms-0 ms-sm-auto d-block d-sm-flex subscribe <?php if($cUser->getIsSubbed($user->getID())) { echo "active"; } elseif(!$loggedIn) { echo "disabled"; } ?>" id="btnsub" onclick="subscribe(<?php echo $user->getID(); ?>)"><?php if($cUser->getIsSubbed($user->getID())) { echo "Subscribed"; } else { echo "Subscribe"; } ?></a>
   </div>
 </div>
</div>
<div class="card shadow-sm mt-3 card-2">

<nav class="bg-body-tertiary">
  <div class="nav nav-tabs mt-2 nav-fill" id="nav-tab" role="tablist">
    <div class="ms-2"></div>
    <button class="nav-link active nav-custom-1" data-bs-toggle="tab" data-bs-target="#videos" type="button" role="tab" aria-selected="true">Videos</button>
    <button class="nav-link nav-custom-2" data-bs-toggle="tab" data-bs-target="#about" type="button" role="tab" aria-selected="false">About</button>
    <?php if($cUser->getIsAdmin()) { ?><button class="nav-link nav-custom-3 admin" data-bs-toggle="tab" data-bs-target="#admin" type="button" role="tab" aria-selected="false">Admin</button><?php } ?>
    <div class="me-2"></div>
  </div>
</nav>
<div class="tab-content" id="nav-tabContent">
  <div class="card-body tab-pane fade show active card-child-1" id="videos" role="tabpanel" tabindex="0">
   <div class="row row-cols-auto g-3">
<?php 
$videos = $user->getVideos($con, 0, 32); 
foreach($videos as $video) {
$video = new Video($con, $video['watch']);
?>
        <div class="col-12 col-md-4 col-lg-3 col-xl-3 col-xxl-3" style="cursor: pointer;" onclick="location.href = '//<?php echo $_SERVER['HTTP_HOST']; ?>/watch?v=<?php echo $video->getWatchID(); ?>'">
          <div class="card shadow-sm h-100 w-100">
           <div class="position-relative d-inline-block">
           <div class="ratio ratio-16x9">
              <img src="<?php echo $video->getThumbnail(); ?>" class="rounded-end rounded-start" alt="<?php echo $video->getTitle(); ?>">
              </div>
            <span class="position-absolute bottom-0 end-0 badge text-bg-dark mb-1 me-1 opacity-75"><?php echo $video->getDuration(); ?></span>
           </div>
            <div class="card-body">
              <h5 class="card-title text-break"><?php echo $video->getTitle(); ?></h5>
              <!--<span class="card-text text-truncate d-inline-block" style="max-width: 100%;">Description (wont exist loool)</span>-->
               <a class="text-decoration-none text-truncate" href="/channel/<?php echo $video->getCreator(false); ?>"><?php echo $video->getCreator(); ?></a>
               <br><?php echo $video->getViews(true); ?><i class="bi bi-dot"></i><?php echo $video->getDate(); ?>
            </div>
          </div>
        </div>
      <?php }
      if(count($videos) <= 0) { echo '<h2 class="text-reset mx-auto mb-4 mt-4 text-center"><span class="bi bi-egg-fried" style="font-size: 5rem;"></span> <br> No videos have been found.</h2>'; } ?>
      </div></div>
  <div class="card-body tab-pane fade card-child-2" id="about" role="tabpanel" tabindex="0">
  <div class="d-flex mb-2"><span class="me-auto"><?php echo $user->getBio(); ?></span>  <a class="btn btn-primary fs-4 bi bi-discord me-2"></a> <a class="btn btn-danger fs-4 bi bi-youtube"></a></div>
  <span class="text-muted">Joined:</span> <?php echo $user->getDate(); ?>
  </div>
  <?php if($cUser->getIsAdmin()) { ?><div class="card-body tab-pane fade card-child-3 admin" id="admin" role="tabpanel" tabindex="0">ban or sidasd fsdo</div><?php } ?>
</div>
</div>
</main>
<?php echo $user->getCustomJS(); ?>
</body>
</html>