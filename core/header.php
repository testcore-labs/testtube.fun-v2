<?php
use Astrotomic\Twemoji\Twemoji;
// Baseline function file and for the header
require basename(__DIR__).'/config.php';
require_once basename(__DIR__).'/../vendor/autoload.php';
require_once basename(__DIR__).'/classes/user.php';

// User shieet
if(empty($_SESSION['user'])) {
 $cUser = new User($con, 0); // for now... lol
 $loggedIn = false;
} else {
 require_once 'core/classes/strikes.php';
 $cUser = new User($con, $_SESSION['user']);
 $loggedIn = true;
 $strikes = New Strikes($con, $cUser->getID(), true);
 $isBanned = $strikes->getStrikeCount();
 if($_SERVER['PHP_SELF'] !== "/banned.php") {
  if($isBanned >= 3) {
  header("Location: /banned");
  }
 }
}

function twemoji($emoji) {
$remoji = Array('😭','😡','😵','💀','🤕', '🤬', '🌞', '😱', '🙄', '😀', '😎', '😂', '👍', '✅', '❌', '🤮', '🥰', '🍴', '😡', '☺️', '☠️', '🥴', '🤯', '😤', '🥶');
if(empty($emoji)) {
$emoji = $remoji[array_rand($remoji)];
}
return Twemoji::emoji($emoji)->url();
}

function embed($description = null) {
  require basename(__DIR__).'/config.php';
  if(empty($description)) {
   $description = "Videos for everyone";
  }
 return '<meta property="og:site_name" content="'.$sitename.'">
<meta property="og:image" content="https://'.$_SERVER["HTTP_HOST"].'/assets/img/icon.png"/>
<meta content="'.$pagename.'" property="og:title">
<meta content="'.$description.'" property="og:description">
<meta name="theme-color" content="#0078fa">';
}

function logo() {
return '<a class="navbar-brand ms-2 my-auto" href="/"><img width=110 src="/assets/img/icon.png" class="rounded-5 me-2" alt="Icon"></a>';
}


function head() {
$form = '<h1>noob key pls</h1>
<form method="POST">
<input type="password" placeholder="KEYYYYYY" name="pw">
<input type="submit" value="submit">
</form>';
$key = "testtub123";
if(!true) {
if(empty($_POST['pw'])) {
  if(empty($_SESSION['pw'])) {
    die($form);
  }
} else {
$_SESSION['pw'] = $_POST['pw'] ?? NULL;
if($_SESSION['pw'] !== $key) {
  die('<h1 style="color: red;">incorrect...</h1>'.$form);
}
}
}
return '
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="preload" href="/assets/img/icon.png" as="image">
<script rel="preload" src="/assets/js/particle.js"></script>
<script rel="preload" src="/assets/js/actions.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
'.bgeffect().'
';
}


function bgeffect() {
return '<div id="particles" class="position-fixed sticky-top z-n1 h-100" style="width: 95%;" alt=""></div>';
}


function navbar() {
if(isset($_GET['q'])) {
 $searchValue = ' value="'.$_GET['q'].'"';
} else {
 $searchValue = " ";
}
  return '
<nav class="navbar navbar-expand-md bg-body-tertiary sticky-top">
<div class="container-fluid">
<div class="d-flex">
<button class="btn btn bi bi-list fs-4 text-reset" type="button" data-bs-toggle="offcanvas" data-bs-target="#menu">
</button>
'.logo().'
</div>
<div class="navbar-collapse" id="navbarText">
    <form class="d-flex ms-auto my-1 my-sm-1 my-lg-0 w-auto" role="search" action="/search">
     <input class="form-control rounded-end-0" type="search" placeholder="Search..." aria-label="Search" name="q"'.$searchValue.'>
     <button class="btn btn-primary bi bi-search rounded-start-0" type="submit"></button>
    </form>
  </div>
</div>
</nav>

'.sidebar().'
</div>


<div class="py-1 rounded-0 alert alert-warning text-center">This website is in public beta. <code>INFO: DOMAIN: '.$_SERVER['HTTP_HOST'].'; PHP: '.phpversion().'; REQUEST TIME: '.$_SERVER['REQUEST_TIME'].'</code></div>
';
}


function sidebar() {
if(empty($_SESSION['user'])) {
 $top = '
 <a class="btn btn-primary w-100 d-flex" href="/login"><i class="bi bi-door-open fs-5 ms-auto"></i> <span class="my-auto text-center ms-1 me-auto">Login</span></a>';
} else {
 require basename(__DIR__).'/config.php';
 $randomWelcome = array(
 "Welcome back", "Ahoy", "Wassup", "Sup", "How ya doin'", "Hey", "Hola", "Hi", "Hello", "Hewwo", "Bonjour", "Labas"
 );
 $randomWelcome = $randomWelcome[array_rand($randomWelcome, 2)[0]];

 $cUser = new User($con, $_SESSION['user']);

 $subs = '<hr>';
 $subList = $cUser->getYourSubs();
 foreach($subList as $sub) {
 $subUser = new User($con, $sub['user']);
 $subs .= '
        <li>
         <a href="/channel/'.$subUser->getUsername(false).'" class="nav-link py-1 px-2 text-body">
          <img class="rounded-circle me-2" src="'.$subUser->getAvatar().'" alt="" style="width: 25px; height: 25px;">
          '.$subUser->getUsername(false).'
         </a>
        </li>';
}
if(count($subList) == 0) {
 $subs = " ";
}
 $top = '<div class="d-flex mb-2 min-w-100">
  <img class="rounded-circle me-2" width=48 height=48 src="'.$cUser->getAvatar().'" alt="'.$cUser->getUsername(false).'">
  <span class="h5 my-auto text-body">'.$randomWelcome.', <span class="h5">'.$cUser->getUsername(false).'</span></span>
 </div>
 <div class="d-flex">
  <a class="btn btn-success bi bi-upload me-1 fs-5" href="/upload"></a> <a class="btn btn-primary bi bi-gear me-1 fs-5" href="/settings"></a> <a class="btn btn-danger bi bi-door-open ms-auto fs-5" href="/logout"></a>
 </div>
 <hr>
<ul class="nav nav-pill flex-column">
      <li class="nav-item">
        <a href="/channel/'.$cUser->getUsername(false).'" class="nav-link py-1 px-2 text-body" aria-current="page">
         <i class="bi bi-person me-2 fs-5 p-0"></i>
          Your channel
        </a>
      </li>
      <li>
        <a href="/history" class="nav-link py-1 px-2 text-body">
          <i class="bi bi-clock me-2 fs-5 p-0"></i>
          History
        </a>
      </li>
'.$subs.'
</ul>

';
}

return '<div class="offcanvas offcanvas-start" tabindex="-1" id="menu" style="width: 15rem;">
<div class="d-flex bg-body-tertiary">
  <div class="my-2 mx-2">
  <button type="button" class="btn bi bi-list fs-4 text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>'.logo().'
  </div>
</div>
<div class="offcanvas-body">
'.$top.'
</div>';
}
