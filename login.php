<!doctype html>
<?php 
ob_start();
session_set_cookie_params(2592000);
session_start();
require 'core/header.php';

// As clean as possible.. ;(

$error = null;

if($loggedIn) {
 header("Location: /");
}

if(isset($_POST['password']) && isset($_POST['username']) && isset($_POST['submit'])) {
 $id = $cUser->login($_POST['username'], $_POST['password']);
 if(is_int($id)) {
  $_SESSION['user'] = $id;
  header("Location: /");
 } else {
  if(empty($_POST['username']) || strlen($_POST['username']) < 0) {
   $error .= "Username field is empty. ";
  } elseif(empty($_POST['password']) || strlen($_POST['password']) < 0) {
   $error .= "Password field is empty. ";
  } elseif(!is_int($id)) {
   $error .= "Username or password is incorrect. "; // fuk u im not telling u the secretz...
  }
 }
}

$randomFact = json_decode(file_get_contents("https://uselessfacts.jsph.pl/api/v2/facts/random"), TRUE);
$randomFact = $randomFact['text'];
?>
<!doctype html>
<html lang="en" data-bs-theme="dark">
<head>
  <?php echo head(); ?>
  <title><?php echo $pagename." | ".$sitename; ?></title>
  <?php echo embed("Login to enjoy more of this website."); ?>
</head>
<body>
<?php echo navbar(); ?>
  <main class="container-fluid mt-3">
   <?php if(isset($error)) { ?>
   <div class="alert alert-danger d-flex"><i class="bi bi-exclamation-lg fs-4"></i> <span class="my-auto"><?php echo $error; ?></span></div>
   <?php } ?>
   <div class="d-flex flex-column flex-lg-row-reverse">

   <div class="col card shadow-sm m-2">
   <div class="card-header h3">Login</div>
    <div class="card-body">
     <form method="POST">
      <div class="input-group mb-2">
       <input type="text" class="form-control" placeholder="Username" name="username">
      </div>
      <div class="input-group mb-3">
       <input type="password" class="form-control" placeholder="Password" name="password">
      </div>
      <input class="btn btn-primary" type="submit" name="submit" value="Login">
     </form>
    </div>
    <div class="card-footer text-muted">
     Don't have an account? <a href="/register" class="mt-auto text-decoration-none">Register</a> instead.
    </div>
   </div>
   
   
   <div class="col card shadow-sm m-2">
    <div class="card-header h3">Funfact</div>
    <div class="card-body">
     <?php echo htmlspecialchars($randomFact); ?>
    </div>
   </div>
   
   </div>
  </main>
</body>
</html>
