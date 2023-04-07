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

if(isset($_POST['password']) && isset($_POST['cpassword']) && isset($_POST['username']) && isset($_POST['submit'])) {
  if(empty($_POST['username']) || strlen($_POST['username']) < 0) {
    $error .= "Username field is empty. ";
   } elseif(strlen($_POST['username']) < 3) {
    $error .= "Your username needs atleast 3 characters. ";
   } elseif(preg_match('/[^a-zA-Z0-9\.(_).( )]/', $_POST['username']) >= 1) {
    $error .= "Your username can only contain alphanumeric characters and underscores (_). ";
   } elseif(empty($_POST['password']) || strlen($_POST['password']) < 0) {
    $error .= "Password field is empty. ";
   } elseif(empty($_POST['cpassword']) || strlen($_POST['cpassword']) < 0) {
    $error .= "Confirm password field is empty. ";
   } elseif(strlen($_POST['password']) < 8) {
    $error .= "Your password needs atleast 8 characters. ";
   }
   
$username  = htmlspecialchars($_POST['username']);
$password = htmlspecialchars($_POST['password']);
$cpassword = htmlspecialchars($_POST['cpassword']);

if($password == $cpassword) {
$query = $con->prepare('SELECT * FROM users WHERE username=:un');
$query->bindParam(':un', $username);
$query->execute();
$userExists = count($query->fetchAll());
if($userExists >= 1) {
$error .= "Username already taken. ";
} else {
$password =  hash("sha512", $password);
$bio = "Hello! I am new to TestTube";
$time = time();
$avatar = "/assets/img/avatar-".mt_rand(1, 4).".png";
$query = $con->prepare('INSERT INTO users (username, password, bio, avatar, date) VALUES (:username, :password, :bio, :avatar, :time)');
$query->bindParam(':username', $username);
$query->bindParam(':password', $password);
$query->bindParam(':bio', $bio);
$query->bindParam(':avatar', $avatar);
$query->bindParam(':time', $time);
$query->execute();

$query = $con->prepare('SELECT * FROM users WHERE username=:un');
$query->bindParam(':un', $username);
$query->execute();
$userId = $query->fetch();
$_SESSION['user'] = $userId['id'];
header("Location: /");
}
} else {
$error .= "Passwords do not match. ";
}
}

$randomFact = json_decode(file_get_contents("https://uselessfacts.jsph.pl/api/v2/facts/random"), TRUE);
$randomFact = $randomFact['text'];
?>
<html lang="en" data-bs-theme="dark">
<head>
  <?php echo head(); ?>
  <title><?php echo $pagename." | ".$sitename; ?></title>
  <?php echo embed("Register an account!"); ?>
</head>
<body>
<?php echo navbar(); ?>
  <main class="container-fluid mt-3">
   <?php if(isset($error)) { ?>
   <div class="alert alert-danger d-flex"><i class="bi bi-exclamation-lg fs-4"></i> <span class="my-auto"><?php echo $error; ?></span></div>
   <?php } ?>
   <div class="d-flex flex-column flex-lg-row-reverse">

   <div class="col card shadow-sm m-2">
   <div class="card-header h3">Register</div>
    <div class="card-body">
     <form method="POST">
      <div class="input-group mb-2">
       <input type="text" class="form-control" placeholder="Username" name="username">
      </div>
      <div class="input-group mb-3">
       <input type="password" class="form-control" placeholder="Password" name="password">
      </div>
      <div class="input-group mb-3">
       <input type="password" class="form-control" placeholder="Confirm password" name="cpassword">
      </div>
      <input class="btn btn-primary" type="submit" name="submit" value="Register">
     </form>
     </div>
    <div class="card-footer text-muted">
     Already have an account? <a href="/login" class="mt-auto text-decoration-none">Login</a> instead.
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
