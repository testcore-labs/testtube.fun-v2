<!doctype html>
<?php 
ob_start();
session_set_cookie_params(2592000);
session_start();
require 'core/header.php';

// why did i even add this shits
$errorBottom = Array(
"What are you doing?!",
"Bill Gates is a demon",
"Stop attacking, immediately!",
"Don't do this!",
"Windows To Go With Windows 7",
"half life 3 confirmed",
"my favorite class is the engineer",
"Garry's Mod. That's all I have to say.",
"No! GORDON!",
"Time Doctor Freeman?",
"CS 1.6 FIRE ON GOD 🔥 🔥 ",
"When life gives you lemons, you make lemon grenades.",
"Ah, hello Gordon Freeman",
"the fitness grampacet testis amutli",
"Hop on AOL",
"You've got mail!",
"This ain't no watervideo.",
"funky tiem",
"<a href='//grublox.com/' class='text-decoration-none'>Grublock</a>",
"SHTAP.",
"Works on my machine 🍵",
"Microsoft Edge kinda stupid ngl",

"funfact: most of these error messages were made by some members of some gc."
);

if($_GET['err'] == 404) {
$errorTop = "404 Not found";
} elseif($_GET['err'] == 403) {
  $errorTop = "403 Forbidden";
} elseif($_GET['err'] == 504) {
  $errorTop = "Original TestTube moment.";
} else {
header("Location: /");
}
?>
<html lang="en" data-bs-theme="dark">
<head>
  <?php echo head(); ?>
  <title><?php echo $errorTop." | ".$sitename; ?></title>
</head>
<body>
<?php echo bgeffect(); ?>
  <main class="container-fluid text-center">
   <div class="d-flex flex-column justify-content-center align-items-center min-vh-100">
    <div class="card">
     <div class="card-body p-4">
      <img class="img-fluid mb-2" width=200 src="<?php echo twemoji(''); ?>">
      <h1><?php echo $errorTop; ?></h1>
      <h3 class="text-body"><?php echo $errorBottom[array_rand($errorBottom)]; ?></h3>
      <a class="btn btn-primary mt-2" onclick="window.history.go(-1); return false;">Go back</a>
     </div>
    </div>
   </div>
  </main>
</body>
</html>
