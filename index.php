<!doctype html>
<?php 
ob_start();
session_set_cookie_params(2592000);
session_start();
require 'core/header.php';
require 'core/classes/videos.php';
require 'core/classes/video.php';
?>
<html lang="en" data-bs-theme="dark">
<head>
  <?php echo head(); ?>
  <title><?php echo $pagename." | ".$sitename; ?></title>
  <?php echo embed(); ?>
</head>
<body>
<?php echo navbar(); ?>
  <main class="container-fluid mt-3">
    <?php if(isset($_REQUEST['danger']) || isset($_REQUEST['success'])) { ?>
    <div class="alert alert-<?php if(isset($_REQUEST['danger'])) { echo 'danger d-flex"><i class="bi bi-exclamation-lg fs-4 me-1"></i>'; } elseif(isset($_REQUEST['success'])) { echo 'success d-flex"><i class="bi bi-check-lg fs-4 me-1"></i>'; } ?> <span class="my-auto"><?php if(isset($_REQUEST['danger'])) { echo htmlspecialchars($_REQUEST['danger']); } elseif(isset($_REQUEST['success'])) { echo htmlspecialchars($_REQUEST['success']); } ?></span></div>
    <?php } ?>
    <div class="d-flex flex-column-reverse flex-md-column-reverse flex-xl-column-reverse flex-xxl-row mb-3 align-content-start">
     <div class="flex-grow-1 w-100">
      <div class="row row-cols-auto g-3">
      <?php 
      $videos = getVideos($con, 0, 64, TRUE); 
      foreach($videos as $video) {
      $video = new Video($con, $video['watch'])
      ?>
        <div class="col-12 col-md-4 col-lg-3 col-xl-3 col-xxl-3" style="cursor: pointer;" onclick="location.href = '/watch?v=<?php echo $video->getWatchID(); ?>'">
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
               <a class="text-decoration-none text-truncate" href="/channel/<?php echo $video->getCreator(false); ?>"><?php echo $video->getCreator(); ?></a><br>
               <?php echo $video->getViews(true); ?><i class="bi bi-dot"></i><?php echo $video->getDate(); ?>
            </div>
          </div>
        </div>
      <?php } if(count($videos) <= 0) { echo '<div class="mx-auto text-center fs-4 card shadow-sm w-50"><div class="card-body"><i class="bi bi-egg-fried" style="font-size: 5rem;"></i> <br> No videos have been found.<br>Why dont you <a href="/upload" class="btn btn-success text-capitalize">upload</a> some?</div></div>'; } ?>
    </div>
    </div>
  <div class="card shadow-sm ms-0 ms-xl-0 ms-xxl-2 mb-2 mb-xxl-0 d-flex flex-column align-self-stretch align-self-xl-stretch align-self-xxl-baseline" id="sex" style="min-width: 20rem;">
  <div class="card-header"><?php if(!$loggedIn) { ?> Welcome to <?php echo $sitename; } else { ?>Hello, <?php echo $cUser->getUsername(false); ?><?php } ?></div>
   <div class="card-body"><?php if(!$loggedIn) { ?>
    Hey, welcome back, TestTube has been rewritten from the ground up with a better UI and code base.<br>
    Enjoy the website.<br><br>

    Heres a few reasons why you should sign up:
    <ul>
      <li> A stable and good UI</li>
      <li> Doesn't sell your data</li>
      <li> No ads</li>
      <li> 250 MB limit for uploading</li>
      <ul>
       <li> No paywall</li>
       <li> No limit</li>
      </ul>
    </ul>
    <a class="btn btn-success bi bi-door-open" href="/register"> Register now!</a>
    <br><br>
    Though, all that said, I need to say something.<br>
    Hosting comes at a cost and thats why I plan to make a paypal.
    <ul>
      <li> 1 GB limit for uploading</li>
      <li> Other: </li>
      <ul>
       <li> Custom badge</li>
       <li> Discord role</li>
      </ul>
    </ul>
    The minimum you pay is 1$.
    <br>
    <a class="btn btn-primary bi bi-paypal mt-1" href="https://paypal.me/qzip"> Donate now</a>
    <a class="btn btn-secondary bi bi-discord mt-1" href="//discord.gg/idk16">  Join our Discord</a>
    <?php } else { ?>
     <h4>News</h4>
     <hr>
     There is absolutely nothing new on this wasteland of a site.
    <?php }  ?>
   </div>
  </div>
</div>
  </main>
</body>
</html>
