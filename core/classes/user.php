<?php
class User {

    private $con, $sqlData;

    public function __construct($con, $id) {
	 $query = $con->prepare('SELECT * FROM users WHERE id=:id');
	 $query->bindParam(':id', $id);
     $query->execute();
	 $this->con = $con;
	 $this->id = $id;
     $this->sqlData = $query->fetch(PDO::FETCH_ASSOC) ?? 0;
    }

	public function getID() {
	    return $this->sqlData["id"] ?? 0;
	}
	
	public function getUsername($icon = true) {
		require_once __DIR__.'/strikes.php';
		$strikes = new Strikes($this->con, $this->sqlData['id'] ?? 0, true);
		$username = $this->sqlData["username"];
		if($icon) {
		$icon = "";
		if($strikes->getStrikeCount() >= 3) {
		$icon .= '<span class="badge bg-danger">Banned</span> ';
		}
		if($this->getIsVerified()) {
		$icon .= '<i class="bi bi-patch-check"> </i>';
		}
		if($this->getIsAdmin()) {
		$icon .= '<i class="bi bi-shield-check"> </i>';
		}
		return $icon.htmlspecialchars($username);
		};
		return htmlspecialchars($username);
	}

	public function getPassword() {
		return $this->sqlData["password"];;
	}
	
	public function getBio($nobr = false) {
		if($nobr) {
		return htmlspecialchars($this->sqlData["bio"]);		
		} else {
	    return nl2br(htmlspecialchars($this->sqlData["bio"]));		
	    }
	}

    public function getVideos($con, $isPrivate, $limit = NULL) {
		$query = $con->prepare('SELECT watch FROM videos WHERE privacy=:privacy AND creator=:creator LIMIT :limit');
		$query->bindParam(':privacy', $isPrivate, PDO::PARAM_INT);
		$query->bindParam(':limit', $limit, PDO::PARAM_INT);
		$query->bindParam(':creator', $this->sqlData["id"], PDO::PARAM_INT);
		$query->execute();
		return $query->fetchAll(PDO::FETCH_ASSOC);
	}

	public function getCustom($nostyle = false) {
		if(!$nostyle) {
	    return '<!-- User Sheet
		For anyone reading this,
		card-1 is the very top card
		card-2 is the card shadow-sm that you see on the video select and stuff
		subscribe is the button for subbing
		subcount is the sub count
		avatar is the profile picture
		username is the username
		
		card-child-* are the children of the navbar selecting.
		nav-custom-* are the navbar buttons themselves, for the about & videos.
		
		the rest is untouched, you can modify anything.

		for those that are undocumented idk lol
        -->		
		<style type="text/css">'.htmlspecialchars($this->sqlData["custom"] ?? NULL, ENT_NOQUOTES).'</style>
		<!-- user sheet -->';
		} else {
		return htmlspecialchars($this->sqlData["custom"] ?? NULL, ENT_NOQUOTES);
		}
	}

	public function getCustomJS($noscript = false) {
		if(!$noscript) {
	    return '<script>'.$this->sqlData["js"].'</script>';
		} else {
		return $this->sqlData["js"];
		}
	}

	public function getAvatar() {
	    return $this->sqlData["avatar"];
	}
	
	public function getBanner() {
	    return $this->sqlData["banner"];
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
        //$time_ago = $time_ago - 2; // MAN DO I EVER LOVE PHP GUYS!
        return $time_ago . " " . $unit . ($time_ago > 1 ? "s" : "") . " ago";
	}

	public function getIsAdmin() {
	    if($this->sqlData["admin"] ?? 0 == 1) {
		 return true;
		} else {
		 return false;
		}
	}

	public function getIsVerified() {
	    if($this->sqlData["verified"] ?? 0 == 1) {
		 return true;
		} else {
		 return false;
		}
	}

    // do not TOUCH!
	public function login($username, $password) {
        // lets start this bitch
		$query = $this->con->prepare('SELECT id, password FROM users WHERE username=:user');
		$query->bindParam(':user', $username);
		$query->execute();
		$fetch = $query->fetch(PDO::FETCH_ASSOC);
		if(empty($fetch)) {
		 return false;
		}
        if($fetch['password'] == hash("sha512", $password)) {
		 return $fetch['id'];
		} else {
		 return false;
		}
	}

    // sub scrib to meh
	public function addSub() {
		$query = $this->con->prepare("SELECT id FROM subscribers WHERE subscriber=:sub AND user=:user");
		$query->bindParam(":sub", $_SESSION['user']);
		$query->bindParam(":user", $this->id, PDO::PARAM_INT);
		$query->execute();
		$subs = $query->fetchAll(PDO::FETCH_ASSOC);
		if(empty($subs)) {
		$subs = 0;
		} else {
		$subs = count($subs);
		}

		if($subs == 0) {
		 $time = time();
	 	 $query = $this->con->prepare("INSERT INTO subscribers (subscriber, user, date) VALUES (:sub, :user, :time)");
		 $query->bindParam(":sub", $_SESSION['user']);
		 $query->bindParam(":user", $this->id, PDO::PARAM_INT);
		 $query->bindParam(":time", $time, PDO::PARAM_INT);
		 $query->execute();
		 return Array("response" => 1, "value" => $this->getSubs(true));
		} else {
		 $query = $this->con->prepare("DELETE FROM subscribers WHERE subscriber=:sub AND user=:user");
		 $query->bindParam(":sub", $_SESSION['user']);
		 $query->bindParam(":user", $this->id, PDO::PARAM_INT);
		 $query->execute();
		 return Array("response" => 0, "value" => $this->getSubs(true));
	   }
	}

	public function getSubs($short = true) {
		$query = $this->con->prepare("SELECT id FROM subscribers WHERE user=:user");
		$query->bindParam(":user", $this->id, PDO::PARAM_INT);
		$query->execute();
		$subs = $query->fetchAll(PDO::FETCH_ASSOC);
		if(empty($subs)) {
		$subs = 0;
		} else {
		$subs = count($subs);
		}

		
		if ($short == "true") {
		
			if ($subs < 1000000) {
			 // Anything less than a million
			 return number_format($subs);
			} else if ($subs < 1000000000) {
			 // Anything less than a billion
			 return number_format($subs / 1000000, 1) . 'M';
			} else {
			 // At least a billion
			 return number_format($subs / 1000000000, 1) . 'B';
			}
			} else {
			return $subs;
		}
	}

	public function getYourSubs() {
		$query = $this->con->prepare("SELECT * FROM subscribers WHERE subscriber=:sub");
		$query->bindParam(":sub", $this->id, PDO::PARAM_INT);
		$query->execute();
		return $query->fetchAll(PDO::FETCH_ASSOC);
	}

	public function getIsSubbed($user) {
		$query = $this->con->prepare("SELECT id FROM subscribers WHERE subscriber=:sub AND user=:user");
		$query->bindParam(":sub", $this->id, PDO::PARAM_INT);
		$query->bindParam(":user", $user, PDO::PARAM_INT);
		$query->execute();
		$subs = $query->fetchAll(PDO::FETCH_ASSOC);
		if(empty($subs)) {
		return false;
		} else {
		return true;
		}
	}
}