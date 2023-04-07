<!doctype html>
<?php 
ob_start();
session_set_cookie_params(2592000);
session_start();
require 'core/header.php';
require 'core/classes/videos.php';
require 'core/classes/video.php';

// i love u chatgpt... its because im lazy to do pagination -qzeepi
// Define the number of videos to display per page
$videosPerPage = 16;

$query = isset($_GET['q']) ? $_GET['q'] : " ";
// Get the current page number from the URL query string
$page = isset($_GET['p']) ? (int)$_GET['p'] : 1;

// Calculate the start and end indexes for the videos to display on the current page
$start = ($page - 1) * $videosPerPage;
$end = $start + $videosPerPage;
?>
<html lang="en" data-bs-theme="dark">
<head>
  <?php echo head(); ?>
  <title><?php echo $pagename." | ".$sitename; ?></title>
  <?php echo embed($query); ?>
</head>
<body>
<?php echo navbar(); ?>
  <main class="container-fluid mt-3">
      <div class="flex flex-column gap-3">
      <?php
      if(strlen($query) < 1) {
       die('<div class="mx-auto text-center fs-4 card shadow-sm w-50"><div class="card-body"><i class="bi bi-egg-fried" style="font-size: 5rem;"></i> <br> You need to have 1 character or more to make a search.</div></div>');
      }
      $videos = getVideos($con, 0, NULL, FALSE, $query); 
      $videosCount = count($videos);
      ?>
      <div class="hstack gap-3 card py-2 px-3 bg-body-tertiary">
       <div class="fs-5">Results (<?php echo $videosCount; ?>)</div>
       <div class="ms-auto btn-group">
        <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
        Order by
        </button>
       <ul class="dropdown-menu">
        <li><a class="dropdown-item" href="#">Descending (newest first)</a></li>
        <li><a class="dropdown-item" href="#">Ascending (oldest first)</a></li>
       </ul>
       </div>
      </div>
      <?php
      // Slice the array to only include the videos for the current page
      $videos = array_slice($videos, $start, $videosPerPage);
      foreach($videos as $video) {
      $vvideo = new Video($con, $video['watch'])
      ?>
        <div class="card shadow-sm mt-3" style="cursor: pointer;" onclick="location.href = '/watch?v=<?php echo $vvideo->getWatchID(); ?>'">
          <div class="row g-0">                                                                                                                                                                                                                                                                                                                                                                                                    
            <div class="col-12 col-md-4 col-lg-3 col-xl-2 position-relative d-inline-block">
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
      <?php } if(count($videos) <= 0) { echo '<div class="mx-auto text-center fs-4 card shadow-sm w-50 mt-3"><div class="card-body"><i class="bi bi-egg-fried" style="font-size: 5rem;"></i> <br> No videos found.</div></div>'; } ?>
    </div>
<?php
// Output the pagination links
echo '<nav class="mt-3"><ul class="pagination justify-content-center">';

// Output the "Previous" link, if applicable
if ($page > 1) {
    $prevPage = $page - 1;
    echo '<li class="page-item"><a class="page-link" href="?q='.$query.'&p=' . $prevPage . '">Previous</a></li>';
} else {
    echo '<li class="page-item disabled"><a class="page-link">Previous</a></li>';
}

// Output the numbered page links
$numPages = ceil($videosCount / $videosPerPage);
$ellipsisShown = false;
for ($i = 1; $i <= $numPages; $i++) {
    if ($i == $page) {
        echo '<li class="page-item active"><a class="page-link">' . $i . '</a></li>';
    } elseif ($i == 1 || $i == $numPages || ($i >= $page - 2 && $i <= $page + 2)) {
        echo '<li class="page-item"><a class="page-link" href="?q='.$query.'&p=' . $i . '">' . $i . '</a></li>';
        $ellipsisShown = false;
    } elseif (!$ellipsisShown) {
        echo '<li class="page-item"><a class="page-link">...</a></li>';
        $ellipsisShown = true;
    }
}

// Output the "Next" link, if applicable
if ($page < $numPages) {
    $nextPage = $page + 1;
    echo '<li class="page-item"><a class="page-link" href="?q='.$query.'&p=' . $nextPage . '">Next</a></li>';
} else {
    echo '<li class="page-item disabled"><a class="page-link">Next</a></li>';
}

echo '</ul></nav>';
?>
  </main>
</body>
</html>
