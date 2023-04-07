<?php
require_once __DIR__.'/user.php';
class Strikes {

    private $con, $sqlData;

    public function __construct($con, $id, $isBanned = false) {
	 $query = $con->prepare('SELECT * FROM strikes WHERE user=:id');
	 $query->bindParam(':id', $id);
     $query->execute();
	 $this->con = $con;
	 $this->id = $id;
     if($isBanned) {
     $this->sqlData = $query->fetchAll(PDO::FETCH_ASSOC) ?? 0;
     } else {
     $this->sqlData = $query->fetch(PDO::FETCH_ASSOC) ?? 0;
     }
     $this->user = new User($con, $this->sqlData['admin'] ?? 0);
    }

    public function getAllStrikes() {
	    return $this->sqlData;
	}

	public function getStrikeCount() {
	    return count($this->sqlData);
	}

    public function getAdmin() {
	    return $this->user->getUsername();
	}

    public function getAdminRaw() {
	    return $this->sqlData["admin"];
	}

	public function getNote() {
	    return $this->sqlData["note"];
	}

	public function getDate() {
	    return date('Y-m-d H:i:s', $this->sqlData["date"]);
	}

	public function addStrike($note) {
        $note = nl2br(htmlspecialchars($note));
        $time();
        $query = $this->con->prepare("INSERT INTO strikes (user, admin, note, date) VALUES (:user, :admin, :note, :time)");
        $query->bindParam(":user", $this->id, PDO::PARAM_INT);
        $query->bindParam(":admin", $_SESSION['user'], PDO::PARAM_INT);
        $query->bindParam(":note", $note);
        $query->bindParam(":time", $time, PDO::PARAM_INT);
        $query->execute();
	}
	public function delStrike($id) {
        $query = $this->con->prepare("DELETE FROM strikes WHERE subscriber=:sub AND user=:user ORDER BY desc date");
        $query->bindParam(":sub", $_SESSION['user']);
        $query->bindParam(":user", $this->id, PDO::PARAM_INT);
        $query->execute();
    }
}