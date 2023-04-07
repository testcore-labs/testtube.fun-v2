<!doctype html>
<?php 
ob_start();
session_set_cookie_params(2592000);
session_start();
require 'core/header.php';

$error = null;

if(!$loggedIn) {
  die(header("Location: /login"));
}

$actionAlert = "";

// oh the misery and mess that this will beeeeeeeeeeeee....

// id=asettings
if(isset($_POST['rpassword']) && isset($_POST['password'])) {
  if(hash("sha512", $_POST['rpassword']) == $cUser->getPassword()) {
  $password = hash("sha512", $_POST['password']);
  $query = $con->prepare("UPDATE users SET password=:password WHERE id=:userid");
  $query->bindParam(":password", $password);
  $query->bindParam(":userid", $_SESSION['user']);
  $query->execute();
  $actionAlert = '<div class="alert alert-success d-flex" role="alert"><i class="bi bi-check-lg fs-4 me-1"></i> <span class="my-auto">New password set.</span></div>
';
} else {
  $actionAlert = '<div class="alert alert-danger d-flex" role="alert"><i class="bi bi-exclamation-lg fs-4 me-1"></i> <span class="my-auto">Old password does not match.</span></div>
';
}
} elseif(isset($_POST['rpassword'])) {
  $actionAlert = '<div class="alert alert-danger d-flex" role="alert"><i class="bi bi-exclamation-lg fs-4 me-1"></i> <span class="my-auto">Old password field empty.</span></div>
';
} elseif(isset($_POST['password'])) {
  $actionAlert = '<div class="alert alert-danger d-flex" role="alert"><i class="bi bi-exclamation-lg fs-4 me-1"></i> <span class="my-auto">New password field empty.</span></div>
';
}

// id=psettings
if(isset($_FILES['pfp']['tmp_name'])) {
  $userid = $cUser->getID();
  $pfp = file_get_contents($_FILES['pfp']['tmp_name']);
  $avatarDir = "uploads/avatars/".$userid.".png";
  // everyone uses move_uploaded_file() because its "safer", in reality, it is not needed for this shit tho.
  if(file_put_contents($avatarDir, $pfp)) {
  $avatarDir = "/".$avatarDir;
  $query = $con->prepare("UPDATE users SET avatar=:pfp WHERE id=:userid");
  $query->bindParam(":pfp", $avatarDir);
  $query->bindParam(":userid", $userid);
  $query->execute();
  $actionAlert = '<div class="alert alert-success d-flex" role="alert"><i class="bi bi-check-lg fs-4 me-1"></i> <span class="my-auto">Avatar uploaded.</span></div>
';
  } else {
    $actionAlert = '<div class="alert alert-danger d-flex" role="alert"><i class="bi bi-exclamation-lg fs-4 me-1"></i> <span class="my-auto">Avatar not uploaded.</span></div>';
  }
}

if(isset($_FILES['banner']['tmp_name'])) {
  $userid = $cUser->getID();
  $banner = file_get_contents($_FILES['banner']['tmp_name']);
  $bannerDir = "uploads/banners/".$userid.".png";
  // everyone uses move_uploaded_file() because its "safer", in reality, it is not needed for this shit tho.
  if(file_put_contents($bannerDir, $banner)) {
  $bannerDir = "/".$bannerDir;
  $query = $con->prepare("UPDATE users SET banner=:banner WHERE id=:userid");
  $query->bindParam(":banner", $bannerDir);
  $query->bindParam(":userid", $userid);
  $query->execute();
  $actionAlert = '<div class="alert alert-success d-flex" role="alert"><i class="bi bi-check-lg fs-4 me-1"></i> <span class="my-auto">Banner uploaded.</span></div>
';
  } else {
    $actionAlert = '<div class="alert alert-danger d-flex" role="alert"><i class="bi bi-exclamation-lg fs-4 me-1"></i> <span class="my-auto">Banner not uploaded.</span></div>';
  }
}

if(isset($_POST['aboutme'])) {
  $query = $con->prepare("UPDATE users SET bio=:aboutme WHERE id=:userid");
  $query->bindParam(":aboutme", $_POST['aboutme']);
  $query->bindParam(":userid", $_SESSION['user']);
  $query->execute();
  $actionAlert = '<div class="alert alert-success d-flex" role="alert"><i class="bi bi-check-lg fs-4 me-1"></i> <span class="my-auto">About me set.</span></div>
';
}

if(isset($_POST['profilecss'])) {
  $query = $con->prepare("UPDATE users SET custom=:css WHERE id=:userid");
  $query->bindParam(":css", $_POST['profilecss']);
  $query->bindParam(":userid", $_SESSION['user']);
  $query->execute();
  $actionAlert = '<div class="alert alert-success d-flex" role="alert"><i class="bi bi-check-lg fs-4 me-1"></i> <span class="my-auto">Profile CSS set.</span></div>
';
}

if(isset($_POST['profilejs']) && $cUser->getIsAdmin()) {
  $query = $con->prepare("UPDATE users SET js=:js WHERE id=:userid");
  $query->bindParam(":js", $_POST['profilejs']);
  $query->bindParam(":userid", $_SESSION['user']);
  $query->execute();
  $actionAlert = '<div class="alert alert-success d-flex" role="alert"><i class="bi bi-check-lg fs-4 me-1"></i> <span class="my-auto">Profile JS set.</span></div>
';
}
?>
<html lang="en" data-bs-theme="dark">
<head>
  <?php echo head(); ?>
  <title><?php echo $pagename." | ".$sitename; ?></title>
  <?php echo embed("User settings."); ?>
</head>
<body>
<?php echo navbar(); ?>
  <main class="container-fluid mt-3">
    <?php echo $actionAlert; ?>
   <div class="card d-flex">
    <div class="card-body">
     <div class="d-flex align-items-start flex-column gap-3">
      <div class="nav nav-pills w-100 gap-2 nav-fill" id="v-pills-tab" role="tablist" aria-orientation="vertical">
       <button class="nav-link active border" data-bs-toggle="pill" data-bs-target="#asettings" type="button" role="tab">Account settings</button>
       <button class="nav-link border" data-bs-toggle="pill" data-bs-target="#psettings" type="button" role="tab">Profile settings</button>
       <button class="nav-link border" data-bs-toggle="pill" data-bs-target="#tests" type="button" role="tab">Experiments</button>
       <button class="nav-link border" data-bs-toggle="pill" data-bs-target="#v-pills-messages" type="button" role="tab">prob will be used</button>
       <button class="nav-link border text-danger" data-bs-toggle="pill" data-bs-target="#donot" type="button" role="tab" disabled>Ḋ̸͉̮̟͋o̷̧̐ ̴̧̻͓͗̈n̶̨̂͐͝ọ̶͕̊t̴͖̙͍̽̈̅</button>
      </div>
      <div class="border-bottom w-100"></div>
      <div class="tab-content mx-auto">
       <div class="tab-pane fade show active" id="asettings" role="tabpanel" tabindex="0">
        <div class="d-flex flex-column gap-3 mt-3">
         <form class="card p-3" method="POST">
            Change password
            <input type="password" class="form-control mb-2 mt-1" placeholder="Old password" name="rpassword">
            <input type="password" class="form-control mb-3" placeholder="New password" name="password">
            <button type="submit" class="btn btn-primary">Submit</button>
         </form>
        </div>
       </div>
       <div class="tab-pane fade" id="psettings" role="tabpanel" tabindex="0">
        <div class="d-flex flex-column flex-md-row gap-3">
         <button type="button" class="btn btn-secondary border" data-bs-toggle="modal" data-bs-target="#avatarChanger">
          Change avatar
         </button>
         <button type="button" class="btn btn-secondary border" data-bs-toggle="modal" data-bs-target="#bannerChanger">
          Change banner
         </button>
        </div>
        <div class="d-flex flex-column gap-3 mt-3">
        <form class="card p-3" method="POST">
            About me
            <div class="form-floating mb-3 mt-1">
            <textarea class="form-control" placeholder="Hello!" name="aboutme" style="height: 100px"><?php echo $cUser->getBio(true); ?></textarea>
            <label>About me</label>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
        <form class="card p-3" method="POST">
          Profile CSS (You can use discordapp.com for images.)
         <div class="form-floating mb-3 mt-1">
          <textarea class="form-control" placeholder="Leave a comment here" name="profilecss" style="height: 100px"><?php echo $cUser->getCustom(true); ?></textarea>
          <label>.CSS { color: cool; }</label>
         </div>
         <button type="submit" class="btn btn-primary">Submit</button>
        </form>
        <?php if($cUser->getIsAdmin()) {?>
        <form class="card p-3 border-danger" method="POST">
          Profile JS (ADMIN ONLY!!!)
         <div class="form-floating mb-3 mt-1">
          <textarea class="form-control" placeholder="Leave a comment here" name="profilejs" style="height: 100px"><?php echo $cUser->getCustomJS(true); ?></textarea>
          <label>Any JS works. Use with caution.</label>
         </div>
         <button type="submit" class="btn btn-primary">Submit</button>
        </form><?php } ?>
        </div>
       </div>
       <div class="tab-pane fade" id="tests" role="tabpanel" tabindex="0">
        <img class="img-fluid" src="/assets/img/qz.png">
        <hr>
        <form method="GET">
         <div class="form-check form-switch">
          <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckDefault" name="1">
          <label class="form-check-label" for="flexSwitchCheckDefault">Test1</label>
         </div>
         <div class="form-check form-switch">
          <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckDefault" name="2">
          <label class="form-check-label" for="flexSwitchCheckDefault">Test2</label>
         </div>
         <div class="form-check form-switch">
          <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckDefault" name="3">
          <label class="form-check-label" for="flexSwitchCheckDefault">Test3</label>
         </div>
         <div class="form-check form-switch">
          <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckDefault" name="4">
          <label class="form-check-label" for="flexSwitchCheckDefault">Test4</label>
         </div>
        <button type="submit" class="btn btn-primary mt-2">Save changes</button>
        </form>
       </div>
       <div class="tab-pane fade" id="v-pills-messages" role="tabpanel" tabindex="0">mehh</div>
       <div class="tab-pane fade w-100" id="donot" role="tabpanel" tabindex="0"><video class="ratio ratio-16x9 w-100" controls><source src="https://cdn.discordapp.com/attachments/1085085380136677438/1086371405005869106/cb.mp4"></source></video></div>
      </div>
     </div>
    </div>
   </div>


<!-- pfp shit ughhhhhhhhhhhhhhhhhhhhhh -->
<div class="modal fade" id="avatarChanger" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="staticBackdropLabel">Upload avatar</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form method="POST" enctype="multipart/form-data">
      <div class="modal-body">
        <div class="input-group mb-3">
         <input type="file" class="form-control" name="pfp" id="fileInput" accept="image/*">
         <label class="input-group-text">Avatar</label>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-success">Upload</button>
      </div>
      </form>
    </div>
  </div>
</div>

<!-- Banner shitooo -->
<div class="modal fade" id="bannerChanger" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="staticBackdropLabel">Upload banner</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form method="POST" enctype="multipart/form-data">
      <div class="modal-body">
        <div class="input-group mb-3">
         <input type="file" class="form-control" name="banner" id="fileInput" accept="image/*">
         <label class="input-group-text">Banner</label>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-success">Upload</button>
      </div>
      </form>
    </div>
  </div>
</div>

  </main>
  <script>
    if ( window.history.replaceState ) {
  window.history.replaceState( null, null, window.location.href );
}
</script>
</body>
</html>
