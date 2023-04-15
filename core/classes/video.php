<?php
require_once __DIR__.'/user.php';
class Video {

    private $con, $sqlData;

    public function __construct($con, $id) {
	 $query = $con->prepare('SELECT * FROM videos WHERE watch=:id');
	 $query->bindParam(':id', $id);
     $query->execute();
	 $this->id = $id;
	 $this->con = $con;
     $this->sqlData = $query->fetch(PDO::FETCH_ASSOC);
	 $this->user = new User($con, $this->sqlData['creator'] ?? 0);
    }
	
	public function getID() {
	    return $this->sqlData["id"] ?? NULL;
	}
	
	public function getTitle() {
	    return htmlspecialchars($this->sqlData["title"] ?? NULL);
	}
	
	public function getDescription() {
	    return nl2br(htmlspecialchars($this->sqlData["description"]));
	}
	
	public function getFeatured() {
		$featured = $this->sqlData["featured"];
		if($featured == 1) {
		return true;
		} else {
		return false;
		}
	}
	
	public function getCreator($short = true) {
		if($short) {
	    return $this->user->getUsername();
		} else {
		return $this->user->getUsername(false);
		}
	}

	public function getCreatorRaw() {
	    return $this->user->getID();
	}
    
	public function getAvatar() {
	    return $this->user->getAvatar();
	}
	
	public function getWatchID() {
	     return $this->sqlData["watch"] ?? NULL;
	}

	public function getCustom() {
	    return '<style type="text/css">'.htmlspecialchars($this->sqlData["custom"] ?? NULL, ENT_NOQUOTES).'</style>';
	}
   
	public function getThumbnailBase64() {
		return $this->sqlData["thumbnail"];
    }

	public function getThumbnail() {
		return "/api/thumb?v=".$this->id;
    }

	public function getVideo() {
		return $this->sqlData["file"];
    }

    public function getDuration() {
	   return $this->sqlData["duration"];
    }
	
    public function getPrivacy() {
		return $this->sqlData["privacy"] ?? NULL;
	}

	public function getDate() {
		$time = time() - $this->sqlData["date"]; // get the difference between the current time and the timestamp
        $units = array("second", "minute", "hour", "day", "week", "month", "year");
        $divisors = array(1, 60, 3600, 86400, 604800, 2630880, 31570560);
        for ($i = count($divisors) - 1; $i >= 0; $i--) {
          if ($time >= $divisors[$i]) {
            $time_ago = round($time / $divisors[$i]);
            $unit = $units[$i];
            break;
          }
        }
		if(!empty($time_ago) && !empty($unit)) {
        //$time_ago = $time_ago - 2; // MAN DO I EVER LOVE PHP GUYS!
        return $time_ago . " " . $unit . ($time_ago > 1 ? "s" : "") . " ago";
		} else {
		return "just now";
		}
	}

	public function getViews($short) {
		$query = $this->con->prepare("SELECT id FROM views WHERE video=:watch");
		$query->bindParam(":watch", $this->id);
		$query->execute();
		$views = $query->fetchAll(PDO::FETCH_ASSOC);
		if(empty($views)) {
		$views = 0;
		} else {
		$views = count($views);
		}
		if($views == 1) {
		$viewsEnd = " view";
		} else {
		$viewsEnd = " views";
		}

	    if ($short == "true") {
		
		if ($views < 1000000) {
         // Anything less than a million
         return number_format($views).$viewsEnd;
        } else if ($views < 1000000000) {
         // Anything less than a billion
         return number_format($views / 1000000, 1) . 'M'.$viewsEnd;
        } else {
         // At least a billion
         return number_format($views / 1000000000, 1) . 'B'.$viewsEnd;
		}
		} else {
		return $views;
		}
    }

	public function addViews() {
		$time = time();
		$ip = isset($_SERVER["HTTP_CF_CONNECTING_IP"]) ? $_SERVER["HTTP_CF_CONNECTING_IP"] : $_SERVER["REMOTE_ADDR"];
		$ip = hash("sha512", $ip);
		$query = $this->con->prepare("SELECT * FROM views WHERE ip=:ip AND video=:watch ORDER by DATE DESC");
        $query->bindParam(":ip", $ip);
		$query->bindParam(":watch", $this->id);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_ASSOC) ?? NULL;

		if(empty($result)) {
		$result = array("ip" => 0, "id" => 0);
		}
        if($ip !== $result['ip'] && $this->id !== $result['id']) {
			$query = $this->con->prepare("INSERT INTO views (ip, video, date) VALUES (:ip, :watch, :time)");
			$query->bindParam(":ip", $ip);
			$query->bindParam(":watch", $this->id);
			$query->bindParam(":time", $time, PDO::PARAM_INT);
			$query->execute();
		}
    }

	public function getRatings($rating, $short) {
		$query = $this->con->prepare("SELECT id FROM ratings WHERE video=:watch AND rating=:rating");
		$query->bindParam(":watch", $this->id);
		$query->bindParam(":rating", $rating);
		$query->execute();
		$rating = $query->fetchAll(PDO::FETCH_ASSOC);
		if(empty($rating)) {
		$rating = 0;
		} else {
		$rating = count($rating);
		}

	    if ($short == "true") {
		
		if ($rating < 1000000) {
         // Anything less than a million
         return number_format($rating);
        } else if ($rating < 1000000000) {
         // Anything less than a billion
         return number_format($rating / 1000000, 1) . 'M';
        } else {
         // At least a billion
         return number_format($rating / 1000000000, 1) . 'B';
		}
		} else {
		return $rating;
		}
    }

	public function getRatingsUser($rating, $short, $fromUser) {
		$query = $this->con->prepare("SELECT id FROM ratings WHERE video=:watch AND rating=:rating AND user=:user");
		$query->bindParam(":watch", $this->id);
		$query->bindParam(":rating", $rating);
		$query->bindParam(":user", $fromUser);
		$query->execute();
		$rating = $query->fetchAll(PDO::FETCH_ASSOC);
		if(empty($rating)) {
	    $rating = 0;
		} else {
		$rating = count($rating);
		}

		if ($short == "true") {
		
			if ($rating < 1000000) {
			 // Anything less than a million
			 return number_format($rating);
			} else if ($rating < 1000000000) {
			 // Anything less than a billion
			 return number_format($rating / 1000000, 1) . 'M';
			} else {
			 // At least a billion
			 return number_format($rating / 1000000000, 1) . 'B';
			}
			} else {
			return $rating;
			}
	}

	public function addRating($rating) {
		$query = $this->con->prepare("SELECT id FROM ratings WHERE video=:watch AND user=:user AND rating=:rating");
		$query->bindParam(":watch", $this->id);
		$query->bindParam(":user", $_SESSION['user'], PDO::PARAM_INT);
		$query->bindParam(":rating", $rating, PDO::PARAM_INT);
		$query->execute();
		$ratings = $query->fetchAll(PDO::FETCH_ASSOC);
		if(empty($ratings)) {
		$ratings = 0;
		} else {
		$ratings = count($ratings);
		}

		if($ratings == 0) {
		 $time = time();
	 	 $query = $this->con->prepare("INSERT INTO ratings (rating, video, user, date) VALUES (:type, :watch, :user, :time)");
		 $query->bindParam(":type", $rating);
		 $query->bindParam(":watch", $this->id);
		 $query->bindParam(":user", $_SESSION['user'], PDO::PARAM_INT);
		 $query->bindParam(":time", $time, PDO::PARAM_INT);
		 $query->execute();
		 return Array("response" => 1, "rating" => $this->getRatings($rating, true));
		} else {
		 $query = $this->con->prepare("DELETE FROM ratings WHERE rating=:type AND video=:watch AND user=:user");
		 $query->bindParam(":type", $rating);
		 $query->bindParam(":watch", $this->id);
		 $query->bindParam(":user", $_SESSION['user'], PDO::PARAM_INT);
		 $query->execute();
		 return Array("response" => 0, "rating" => $this->getRatings($rating, true));
	   }
	}

	public function addComment($text) {
		require_once __DIR__.'/user.php';
		$query = $this->con->prepare("SELECT date FROM comments WHERE video=:watch AND user=:user ORDER BY date DESC");
		$query->bindParam(":watch", $this->id);
		$query->bindParam(":user", $_SESSION['user'], PDO::PARAM_INT);
		$query->execute();
		$comments = $query->fetch(PDO::FETCH_ASSOC);
		if(!is_bool($comments)) {
		$timepassed = abs(time() - ($comments["date"]));
		if($timepassed <= 30) { 
			if($timepassed <= 1) { $seconds = "second"; } else { $seconds = "seconds"; }
			return "<span>Wait atleast ".(30 - $timepassed)." ".$seconds." before making more comments.</span>";
		}
	    }
		if(!empty($text)) {
			$time = time();
			$query = $this->con->prepare("INSERT INTO comments (user, video, text, date) VALUES (:user, :watch, :text, :time)");
			$query->bindParam(":user", $_SESSION['user'], PDO::PARAM_INT);
			$query->bindParam(":watch", $this->id);
			$query->bindParam(":text", $text);
			$query->bindParam(":time", $time, PDO::PARAM_INT);
			$query->execute();

			$user = new User($this->con, $_SESSION['user']);
		    return '	<div class="card shadow-sm mb-3">
		<div class="card-body">
		<a class="text-decoration-none text-reset flex-grow-1" href="/channel/'.$user->getUsername(false).'">
		<img class="rounded-5 me-2 float-start" width=48 height=48 src="'.$user->getAvatar().'" alt="">
		<div class="d-flex flex-column">
		<span class="my-auto text-white">'.$user->getUsername().'</span><i class="bi bi-dot"></i>just now</a>
		<span class="text-break">
		 '.nl2br(htmlspecialchars($text)).'
		</span>
		</div>
		 <div class="mt-2 d-flex">
		  <a class="btn btn-dark bi bi-reply-all p-2"> View replies</a>
		 </div>
		</div>         
	  </div>';
		}
	}

	public function getComments() {
		require_once __DIR__.'/user.php';
		$comments = "";
		$query = $this->con->prepare("SELECT * FROM comments WHERE video=:watch ORDER BY date DESC");
		$query->bindParam(":watch", $this->id);
		$query->execute();
		$commentsArr = $query->fetchAll(PDO::FETCH_ASSOC);
		foreach($commentsArr as $comment) {
		$user = new User($this->con, $comment['user']);
		//time
		$time = time() - $comment["date"]; // get the difference between the current time and the timestamp
        $units = array("second", "minute", "hour", "day", "week", "month", "year");
        $divisors = array(1, 60, 3600, 86400, 604800, 2630880, 31570560);
        for ($i = count($divisors) - 1; $i >= 0; $i--) {
          if ($time >= $divisors[$i]) {
            $time_ago = round($time / $divisors[$i]);
            $unit = $units[$i];
            break;
          }
        }
		if(!empty($time_ago) && !empty($unit)) {
        //$time_ago = $time_ago - 2; // MAN DO I EVER LOVE PHP GUYS!
        $comment['date'] = $time_ago . " " . $unit . ($time_ago > 1 ? "s" : "") . " ago";
		} else {
		$comment['date'] = "just now";
		}

		$comments .= '	<div class="card shadow-sm mb-3" id="comment-'.$comment['id'].'">
		<div class="card-body">
		<a class="text-decoration-none text-reset flex-grow-1" href="/channel/'.$user->getUsername(false).'">
		<img class="rounded-5 me-2 float-start" width=48 height=48 src="'.$user->getAvatar().'" alt="">
		<div class="d-flex flex-column">
		<span class="my-auto text-white">'.$user->getUsername().'</span><i class="bi bi-dot"></i>'.$comment['date'].'</a>
		<span class="text-break">
		 '.nl2br(htmlspecialchars($comment['text'])).'
		</span>
		</div>
		 <div class="mt-2 d-flex">
		  <a class="btn btn-dark bi bi-reply-all p-2"> View replies</a>
		 </div>
		</div>         
	  </div>';
		}
		if(isset($comments)) {
		 return $comments;
		}
	}

	public function deleteVideo() {
		unlink(realpath($_SERVER["DOCUMENT_ROOT"]).$this->getVideo());
		$query = $this->con->prepare("DELETE FROM videos WHERE watch=:watch");
		$query->bindParam(":watch", $this->id);
		$query->execute();
	}
}
