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
<?php if(!$videoExists) { ?>
<meta property="og:type" content="video" />
<meta property="og:url" content="//<?php echo $_SERVER['HTTP_HOST']; ?>">
<meta property="og:title" content="Video not found" />
<meta property="og:image" itemprop="image" content="/assets/img/icon.png">
<meta property="og:site_name" content="<?php echo $sitename; ?>">
<?php } else { ?>
<meta property="og:type" content="video" />
<meta property="og:url" content="//<?php echo $_SERVER['HTTP_HOST']; ?>">
<meta property="og:video" content="//<?php echo $_SERVER['HTTP_HOST'].$video->getVideo(); ?>" />
<meta property="og:video:type" content="application/mp4" />
<meta property="og:title" content="<?php echo $video->getTitle(); ?>" />
<meta property="og:image" itemprop="image" content="//<?php echo $_SERVER['HTTP_HOST'].$video->getThumbnail(); ?>"/>
<meta property="og:site_name" content="<?php echo $sitename; ?>">
<?php } ?>
<meta name="theme-color" content="#0078fa">
<style>
.video {
    width: 100%;
    height: 56.25%;
}
</style>
<link
  rel="stylesheet"
  href="https://cdn.jsdelivr.net/npm/@vime/core@^5/themes/default.css"
/>
<script
  type="module" rel="preload"
  src="https://cdn.jsdelivr.net/npm/@vime/core@^5/dist/vime/vime.esm.js"
></script>
  <?php echo head(); ?>
   <title><?php if(!$videoExists) { echo "?"; } else { echo $video->getTitle(); } echo " | ".$sitename; ?></title>
   <?php echo $video->getCustom(); ?>
</head>
<body>
<?php echo navbar(); ?>
<main class="container-fluid mt-3">
<?php if(!$videoExists) { ?>
         <div class="rounded-2 border border-1 p-3 bg-body w-100">
         <span class="h2">Video not found</span>
         <hr>
         <span>We're not sure if the video has been deleted, banned or just straight up doesnt exist.</span>
        </div></main>
<?php die(); }?>
    <div class="d-flex flex-row flex-column flex-md-column flex-lg-column flex-xl-row">
      <div class="w-100 mx-auto mx-lg-auto mx-xl-0">
        <vm-player style="--vm-player-theme: #0078fa">
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
        <div class="card shadow-sm my-2">
         <div class="card-body">
         <div class="d-flex flex-row flex-column">
         <div>
         <h2><?php echo $video->getTitle(); ?></h2>
         <?php echo $video->getViews(true); ?><i class="bi bi-dot"></i><?php echo $video->getDate(); ?>
         </div>
         <div class="mt-3">
         <div class="d-flex flex-row flex-column flex-sm-row">
         <a class="text-decoration-none text-reset me-2" href="/channel/<?php echo $video->getCreator(false); ?>">
         <div class="d-inline-block d-flex flex-row my-auto">
          <img class="rounded-circle me-2" width=54 height=54 src="<?php echo $video->getAvatar(); ?>" alt="<?php echo $user->getUsername(false); ?>">
          <div class="align-self-center"><span class="h6 text-body"><?php echo $video->getCreator(); ?></span>
          <p class="text-muted h6 h5-sm mt-2 subcount" id="subcount"><?php echo $user->getSubs(true); ?> subscribers</p></div>
          </div>
         </a>

         <div class="ms-0 ms-sm-auto mt-2 mt-sm-0">
         <div class="btn-group my-auto w-100">
         <a class="btn btn-danger <?php if($cUser->getIsSubbed($user->getID())) { echo "active"; } elseif(!$loggedIn) { echo "disabled"; } ?>" id="btnsub" onclick="subscribe(<?php echo $user->getID(); ?>)"><?php if($cUser->getIsSubbed($user->getID())) { echo "Subscribed"; } else { echo "Subscribe"; } ?></a>
          <a class="btn btn-success bi bi-hand-thumbs-up<?php if(!$loggedIn) { echo " disabled"; } else {if($video->getRatingsUser(0, false, $_SESSION['user']) >= 1) { echo "-fill"; } }?>" id="like" onclick="rateVideo('<?php echo $video->getWatchID(); ?>', 0)"> <?php echo $video->getRatings(0, true); ?></a>
          <a class="btn btn-danger bi bi-hand-thumbs-down<?php if(!$loggedIn) { echo " disabled"; } else { if($video->getRatingsUser(1, false, $_SESSION['user'] ) >= 1) { echo "-fill"; } } ?>" id="dislike" onclick="rateVideo('<?php echo $video->getWatchID(); ?>', 1)"> <?php echo $video->getRatings(1, true); ?></a>
          <button type="button" class="btn btn-secondary bi bi-share" data-bs-toggle="modal" data-bs-target="#share"></button>
          <?php if($loggedIn) { if($cUser->getID() == $user->getID()) { ?> <a class="btn btn-primary bi bi-gear" href="/edit_video?v=<?php echo $video->getWatchID(); ?>"></a> <?php }} ?>
        </div>
         </div>
         </div>
         </div>
         </div>
         <?php if(!empty($video->getDescription())) { ?>
         <hr>
         <div class="text-wrap text-break overflow-y-scroll" style="max-height: 15rem;">
          <?php echo $video->getDescription(); ?>
         </div>
         <?php } ?>
       </div>
       </div>
  <div class="card">
      <button class="btn text-reset py-2 text-start bi bi-chat-left-text" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasExample" aria-controls="offcanvasExample">
        Comments
      </button>
  </div>
    <div class="offcanvas offcanvas-end" data-bs-scroll="true" data-bs-backdrop="false" tabindex="-1" id="offcanvasExample" aria-labelledby="offcanvasExampleLabel" style="max-width: 35rem;">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title" id="offcanvasExampleLabel">Comments</h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body me-3 me-lg-2">
   <div class="form-floating mb-2">
    <textarea class="form-control" placeholder="Leave a comment here" id="comment-text" style="height: 80px"></textarea>
    <label for="floatingTextarea2">Comment</label>
   </div>
   <button type="button" class="btn btn-primary mb-3 <?php if(!$loggedIn) { echo "disabled"; } ?>" onclick="addComment('<?php echo $video->getWatchID(); ?>')">Comment</button>
        <div id="comments">
          <?php echo $video->getComments(); ?>
        </div>
  </div>
</div>
</div>
      <div class="ms-0 ms-xl-3" style="min-width: 30%; max-width: 100%">
      <?php if($cUser->getIsAdmin()) { ?>
        <div class="alert alert-warning alert-dismissible fade show mt-2 mt-xl-0">
         <h4 class="alert-heading">Quick admin actions</h4>
         Quick actions you can perform on a page for easy moderation.
         <hr>
         <div class="me-0 me-sm-auto hstack gap-2">
          <form method="POST"><button class="btn btn-danger bi bi-hammer" type="submit" name="delete"> Delete video</button></form>
          <a class="btn btn-success bi bi-lightbulb"> Feature video</a>
         </div>
         <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php } ?>
       <div>
       <?php
      $vvideos = getVideos($con, 0, 32, TRUE); 
      foreach($vvideos as $vvideo) {
      $vvideo = new Video($con, $vvideo['watch'])
       ?>
        <div class="card shadow-sm mt-2 mt-sm-2 mt-xl-0 mb-2" style="cursor: pointer;" onclick="location.href = '/watch?v=<?php echo $vvideo->getWatchID(); ?>'">
          <div class="row g-0">                                                                                                                                                                                                                                                                                                                                                                                                    
            <div class="col-12 col-lg-6 position-relative d-inline-block">
              <div class="ratio ratio-16x9">
              <img src="<?php echo $vvideo->getThumbnail(); ?>" class="rounded-end rounded-start" alt="<?php echo $vvideo->getTitle(); ?>">
              </div>
              <span class="position-absolute bottom-0 end-0 badge text-bg-dark mb-1 me-1 opacity-75"><?php echo $vvideo->getDuration(); ?></span>
            </div>
            <div class="col-6">
              <div class="card-body">
                <p class="h6 text-reset text-truncate"><?php echo $vvideo->getTitle(); ?></p>
                <a class="text-decoration-none text-truncate" href="/channel/<?php echo $vvideo->getCreator(false); ?>"><?php echo $vvideo->getCreator(); ?></a><br>
                <?php echo $vvideo->getViews(true); ?><i class="bi bi-dot"></i><?php echo $vvideo->getDate(); ?>
              </div>
            </div>
          </div>
        </div>
        <?php } ?>
       </div>
      </div>
    </div>
  </main>


<div class="modal fade" id="share" data-bs-keyboard="false" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="staticBackdropLabel">Share</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="form-floating">
         <textarea class="form-control" readonly style="height: 5.5rem;" id="floatingTextarea">https://<?php echo $_SERVER['HTTP_HOST']; ?>/watch?v=<?php echo $video->getWatchID(); ?></textarea>
         <label for="floatingTextarea">URL</label>
        </div>
        <h5 class="text-center mt-1 text-body">Or</h5>
        <div class="form-floating">
         <textarea class="form-control" readonly style="height: 6.5rem;" id="floatingTextarea"><iframe width="854" height="480" src="https://<?php echo $_SERVER['HTTP_HOST']; ?>/embed?v=<?php echo $video->getWatchID(); ?>"></iframe></textarea>
         <label for="floatingTextarea">Embed URL</label>
        </div>
      </div>
    </div>
  </div>
</div>

</body>
</html>