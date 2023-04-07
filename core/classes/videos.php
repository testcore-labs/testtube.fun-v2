<?php
    function getVideos($con, $isPrivate, $limit = NULL, $random = FALSE, $search = NULL) {
     if($random) {
      $random = "RAND()";
     } else {
      $random = "date DESC";
     }

     if(isset($search)) {
      $search = 'AND LOWER(title) like LOWER("%'.$search.'%") OR LOWER(description) like LOWER("%'.$search.'%")';
     } else {
      $search = " ";
     }

     if(isset($limit)) {
        $limit = 'LIMIT '.(int)$limit;
     } else {
        $limit = " ";
    }

	 $query = $con->prepare('SELECT watch FROM videos WHERE privacy=:privacy '.$search.'ORDER BY '.$random.' '.$limit);
	 $query->bindParam(':privacy', $isPrivate, PDO::PARAM_INT);
     $query->execute();
     return $query->fetchAll(PDO::FETCH_ASSOC);
    }
