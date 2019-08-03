<?php
require_once("Exercise.class.php");
require_once("Lang.class.php");

if(isset($_GET['del'])) {
        DB::deleteOldDBs();
}

class DB {

private $dbID = null;
private $db = null;

public static $MODE_SIMULATE = "SIMULATION";

public function DB($dbID = null) {
	if($dbID == null) {
		/* create new database instance */
		DB::deleteOldDBs();
		$randID = DB::generateRandomString();
		$this->dbID = $randID;
		$this->db = new PDO("sqlite:DBs/".$randID.".sqlite");
		$this->init_db();
		//DB::generateTable($this->query("SELECT * FROM DORF"));
	} elseif($dbID == DB::$MODE_SIMULATE) {
    $this->db = new DBSimulator();
    $this->init_db();
  } elseif(filesize("DBs/".$dbID.".sqlite")>1024*30) {
		/* DB size too big => create new one */
		unlink('DBs/'.$dbID.'.sqlite');
		DB::deleteOldDBs();
		$randID = DB::generateRandomString();
	        $this->dbID = $randID;
        	$this->db = new PDO("sqlite:DBs/".$randID.".sqlite");
                $this->init_db();
	} else {
		/* use existing database instance */
		$this->dbID = $dbID;
		$this->db = new PDO("sqlite:DBs/".$dbID.".sqlite");
//		$this->init_db();
	}

}

public static function deleteOldDBs() {
	if ($handle = opendir('DBs')) {
	    $deleteBefore = time() - 60*60*36; // 36 hours
	    while (false !== ($file = readdir($handle))) {
        	if ($file != "." && $file != "..") {
		    $filemtime = filemtime('DBs/'.$file);
		    if($filemtime < $deleteBefore) {
		            unlink('DBs/'.$file);
		    }
        	}
	    }
	    closedir($handle);
	}
}

public function getDbID() { return $this->dbID; }

/* code
 * -1: 	error
 *  1:  correct
 */
public static function respondMsg($code, $msg = null, $result = null) {
  $toJson["code"] = $code;
  if($msg != null) { $toJson["msg"] = $msg; }
  if($result != null) { $toJson["result"] = $result; }
	return $toJson;
}



public function query($query, $readonly = FALSE, $exercise = null) {
	$makesResult = TRUE;

	$q = trim($query);

	$semipos = strpos($q, ";");
	if($semipos !== FALSE) {
		$q = substr($q, 0, $semipos);
	}

	/* No SELECT query  */
	if(strtoupper(substr($q, 0, 7)) != "SELECT ") {
		$makesResult = FALSE;
	}

	/* multiple queries (not possible anymore because everything after ; is cut */
	if(strpos($q, ";") !== FALSE) {
		return DB::respondMsg(-1, Lang::txt("Es ist nicht erlaubt, mehrere Anfragen auf einmal auszuführen."));
	}

  if($query == "X") { return DB::respondMsg(1, 'Hey, Schummler!');  }

	$begin = strtoupper(substr($q, 0, 7));
	if($begin != "SELECT " && $begin != "INSERT " && $begin != "UPDATE " && $begin != "DELETE ") {
		return DB::respondMsg(-1, Lang::txt("Erlaubt sind nur SELECT-, INSERT-, UPDATE- und DELETE-Anfragen."));
	}

	if(!$makesResult && $readonly) {
		return DB::respondMsg(-1, Lang::txt("Es sind nur SELECT-Anfragen erlaubt."));
	}

	if(strlen($q) > 1500) {
		return DB::respondMsg(-1, Lang::txt("Wofür brauchst du so eine lange Anfrage?"));
	}

	if(stripos($q, "SELECT ") !== FALSE && stripos($q, "INSERT ") !== FALSE) {
		return DB::respondMsg(-1, Lang::txt("INSERT-SELECT-Kombinationen sind hier nicht erlaubt."));
	}



	if(!$makesResult) {
		$this->db->exec($q);
		$error = $this->db->errorInfo();
    if(strlen($error[2]) < 2) {

		  if($exercise != null && !$exercise->getSolved() && $exercise->getSolution() != null && $exercise->getUpdates() == FALSE) {
		  	return DB::respondMsg(0, Lang::txt("Was machst du da eigentlich?"));
		  }

      $validationError = $this->isCorrect($exercise, $q);
      if($validationError == "") {
        return DB::respondMsg(1, Lang::txt("Yeah")."!");
      } else {
        return DB::respondMsg(-1, $validationError);
      }
    } else {
      return DB::respondMsg(-1, Lang::txt("Fehler").': '.$error[2]);
    }
	}

	$result = $this->db->query($q);
	if($result) {
    $validationError = $this->isCorrect($exercise, $q);
		if($validationError == "") {
      return DB::respondMsg(1, Lang::txt("Yeah")."!", DB::resultsetToArray($result));
    } else {
      return DB::respondMsg(-1, $validationError, DB::resultsetToArray($result));
    }
	} else {
		$error = $this->db->errorInfo();
		return DB::respondMsg(-1, Lang::txt("Fehler").': '.$error[2]);
	}
}

/**
  * @returns
  * emtpy string: correct
  * otherwise: error message
  */
public function isCorrect($exercise, $query) {

	if($query == "CHEAT") {
		return "";
	}

  if($exercise == null) {
    return;
  }

	$query = trim($query);

	$semipos = strpos($query, ";");
	if($semipos !== FALSE) {
		$query = substr($query, 0, $semipos);
	}

  if($exercise->getSolved()) {
    return "";
  }
	if($exercise->getSolution() != null && $exercise->getUpdates() == FALSE) {
		$orderPos = stripos($query, "ORDER BY ");
		if($orderPos !== FALSE) { $query = substr($query, 0, $orderPos); }

		if(!$exercise->getSolved() && strtoupper(substr($query, 0, 7)) != "SELECT ") {
			return Lang::txt("Aber was machst du da eigentlich?");
		}

		$solution = $exercise->getSolution();
		$orderPos  = stripos($solution, " ORDER BY ");
		if($orderPos !== FALSE) { $solution = substr($solution, 0, $orderPos); }

    $resultA = $this->db->query("$query EXCEPT $solution");
		$resultB = $this->db->query("$solution EXCEPT select * from($query)");
		if($resultA && $resultB) {
	                $rowsA = $resultA->fetchAll();
        	        $nA = count($rowsA);

			$rowsB = $resultB->fetchAll();
			$nB = count($rowsB);

			$hint = "";
			if(preg_match('/\'[a-z]*/', $query)) {
				$hint = ' '.Lang::txt("Hinweis: Achte bei Werten, die in Anführungszeichen stehen, auf Groß-/Kleinschreibung.");
			}

			if($nA > 0 && $nB > 0) {
				return Lang::txt("Es kommen falsche Zeilen raus.").$hint;
			}
			elseif($nB > 0) {
				return Lang::txt("Da fehlen noch ein Paar Zeilen.").$hint;
			}
      elseif($nA > 0) {
              return Lang::txt("Es kommen zu viele Zeilen raus.").$hint;
      }

			return "";
		} else {
			$error = $this->db->errorInfo();
			$error_msg = $error[2];
			if(strpos($error_msg, "do not have the same number of result columns") !== FALSE) {
				return Lang::txt("Die Spalten sind falsch.");
			}
			return $error_msg;
		}
        }
	elseif($exercise->getVerificationQuery() != "") {
		$result = $this->db->query($exercise->getVerificationQuery());
		if($result) {
			$rows = $result->fetchAll();
			$n = count($rows);
			if($n == $exercise->getVerificationCount()) {
				return "";
			} else {
				return Lang::txt("Oh oh... Entweder du hast zu viele oder zu wenige Änderungen durchgeführt.");
			}
		} else {
			$error = $this->db->errorInfo();
                        $error_msg = $error[2];
                        return $error_msg;

		}
	}

	return ":-(";
}

public function getPlayerName() {
	$result = $this->db->query("SELECT ".Lang::txt("name")." AS name FROM ".Lang::txt("bewohner")." WHERE ".Lang::txt("bewohnernr")." = 20");
	if($result == FALSE) { return null; }
	foreach($result as $row) {
		return $row['name'];
	}
	return null;
}

//@Deperecated
private static function generateTable($result) {
	if($result == null) { return ""; }
	if($result == "OK") { return "OK"; }
	$html = '<table border="1">';
	$html .= "<tr>";

	for($i = 0; $i < $result->columnCount(); $i++) {
		$col = $result->getColumnMeta($i);
		$html .= "<td><b>".$col['name']."</b></td>";
	}

	$html .= "</tr>";

	$numrows = 0;

	foreach($result as $row) {
		$numrows++;
		if($numrows > 100) { break; }
		$html .= "<tr>";
		foreach($row as $col => $value) {
			if(!is_numeric($col)) {
				$html .= "<td>$value</td>";
			}
		}
		$html .= "</tr>";
	}
	$html .= "</table>";
	return $html;
}

private static function resultsetToArray($result) {
  if($result == null) { return null; }
	if($result == "OK") { return null; }

  $table = array();

  /* start with metadata (column names) */
  $rowdata = array();
  for($i = 0; $i < $result->columnCount(); $i++) {
		$col = $result->getColumnMeta($i);
		$rowdata[] = $col['name'];
	}
  $table[] = $rowdata;

  $numrows = 0;

  /* data */
	foreach($result as $row) {
		$numrows++;
		if($numrows > 100) { break; }
    $rowdata = array();
		foreach($row as $col => $value) {
			if(!is_numeric($col)) {
				$rowdata[] = $value;
			}
		}
		$table[] = $rowdata;
	}
  return $table;
}

public function log($log) {
	file_put_contents("./Logs/".$this->dbID.".log.sql", $log."\n", FILE_APPEND);
}

public function sqlTable($query) {
	return DB::generateTable($this->query($query));
}

private function init_db() {
	$this->db->exec("PRAGMA max_page_count = 30"); // DB size max. 30 KB


$this->db->exec("CREATE TABLE ".Lang::txt("dorf")." (".Lang::txt("dorfnr")." INT PRIMARY KEY, ".Lang::txt("name")." VARCHAR(31),".Lang::txt("haeuptling")." INT);");
        $this->db->exec("INSERT INTO ".Lang::txt("dorf")."(".Lang::txt("dorfnr").",".Lang::txt("name").",".Lang::txt("haeuptling").") VALUES (1, '".Lang::txt("Affenstadt")."', 1);");
        $this->db->exec("INSERT INTO ".Lang::txt("dorf")."(".Lang::txt("dorfnr").",".Lang::txt("name").",".Lang::txt("haeuptling").") VALUES (2, '".Lang::txt("Gurkendorf")."', 6)");
        $this->db->exec("INSERT INTO ".Lang::txt("dorf")."(".Lang::txt("dorfnr").",".Lang::txt("name").",".Lang::txt("haeuptling").") VALUES (3, '".Lang::txt("Zwiebelhausen")."', 13);");

        $this->db->exec("CREATE TABLE ".Lang::txt("bewohner")." (".Lang::txt("bewohnernr")." INTEGER PRIMARY KEY AUTOINCREMENT, ".Lang::txt("name")." VARCHAR(50),".Lang::txt("dorfnr")." INT,".Lang::txt("geschlecht")." CHAR(1),".Lang::txt("beruf")." VARCHAR(31),".Lang::txt("gold")." INT,".Lang::txt("status")." VARCHAR(31));");



	$this->db->exec("INSERT INTO ".Lang::txt("bewohner")." (".Lang::txt("name").",".Lang::txt("dorfnr").",".Lang::txt("geschlecht").",".Lang::txt("beruf").",".Lang::txt("gold").",".Lang::txt("status").") VALUES('".Lang::txt("Paul Backmann")."', 1, '".Lang::txt("m")."', '".Lang::txt("Baecker")."', 850, '".Lang::txt("friedlich")."');");
        $this->db->exec("INSERT INTO ".Lang::txt("bewohner")." (".Lang::txt("name").",".Lang::txt("dorfnr").",".Lang::txt("geschlecht").",".Lang::txt("beruf").",".Lang::txt("gold").",".Lang::txt("status").") VALUES('".Lang::txt("Ernst Peng")."', 3, '".Lang::txt("m")."', '".Lang::txt("Waffenschmied")."', 280, '".Lang::txt("friedlich")."');");
        $this->db->exec("INSERT INTO ".Lang::txt("bewohner")." (".Lang::txt("name").",".Lang::txt("dorfnr").",".Lang::txt("geschlecht").",".Lang::txt("beruf").",".Lang::txt("gold").",".Lang::txt("status").") VALUES('".Lang::txt("Rita Ochse")."', 1, '".Lang::txt("w")."', '".Lang::txt("Baecker")."', 350, '".Lang::txt("friedlich")."');");
        $this->db->exec("INSERT INTO ".Lang::txt("bewohner")." (".Lang::txt("name").",".Lang::txt("dorfnr").",".Lang::txt("geschlecht").",".Lang::txt("beruf").",".Lang::txt("gold").",".Lang::txt("status").") VALUES('".Lang::txt("Carl Ochse")."', 1, '".Lang::txt("m")."', '".Lang::txt("Kaufmann")."', 250, '".Lang::txt("friedlich")."');");
        $this->db->exec("INSERT INTO ".Lang::txt("bewohner")." (".Lang::txt("name").",".Lang::txt("dorfnr").",".Lang::txt("geschlecht").",".Lang::txt("beruf").",".Lang::txt("gold").",".Lang::txt("status").") VALUES('".Lang::txt("Dirty Dieter")."', 3, '".Lang::txt("m")."', '".Lang::txt("Schmied")."', 650, '".Lang::txt("boese")."');");
        $this->db->exec("INSERT INTO ".Lang::txt("bewohner")." (".Lang::txt("name").",".Lang::txt("dorfnr").",".Lang::txt("geschlecht").",".Lang::txt("beruf").",".Lang::txt("gold").",".Lang::txt("status").") VALUES('".Lang::txt("Gerd Schlachter")."', 2, '".Lang::txt("m")."', '".Lang::txt("Metzger")."', 4850, '".Lang::txt("boese")."');");
        $this->db->exec("INSERT INTO ".Lang::txt("bewohner")." (".Lang::txt("name").",".Lang::txt("dorfnr").",".Lang::txt("geschlecht").",".Lang::txt("beruf").",".Lang::txt("gold").",".Lang::txt("status").") VALUES('".Lang::txt("Peter Schlachter")."', 3, '".Lang::txt("m")."', '".Lang::txt("Metzger")."', 3250, '".Lang::txt("boese")."');");
        $this->db->exec("INSERT INTO ".Lang::txt("bewohner")." (".Lang::txt("name").",".Lang::txt("dorfnr").",".Lang::txt("geschlecht").",".Lang::txt("beruf").",".Lang::txt("gold").",".Lang::txt("status").") VALUES('".Lang::txt("Arthur Schneiderpaule")."', 2, '".Lang::txt("m")."', '".Lang::txt("Pilot")."', 490, '".Lang::txt("gefangen")."');");
        $this->db->exec("INSERT INTO ".Lang::txt("bewohner")." (".Lang::txt("name").",".Lang::txt("dorfnr").",".Lang::txt("geschlecht").",".Lang::txt("beruf").",".Lang::txt("gold").",".Lang::txt("status").") VALUES('".Lang::txt("Tanja Trommler")."', 1, '".Lang::txt("w")."', '".Lang::txt("Baecker")."', 550, '".Lang::txt("boese")."');");
        $this->db->exec("INSERT INTO ".Lang::txt("bewohner")." (".Lang::txt("name").",".Lang::txt("dorfnr").",".Lang::txt("geschlecht").",".Lang::txt("beruf").",".Lang::txt("gold").",".Lang::txt("status").") VALUES('".Lang::txt("Peter Trommler")."', 1, '".Lang::txt("m")."', '".Lang::txt("Schmied")."', 600, '".Lang::txt("friedlich")."');");
        $this->db->exec("INSERT INTO ".Lang::txt("bewohner")." (".Lang::txt("name").",".Lang::txt("dorfnr").",".Lang::txt("geschlecht").",".Lang::txt("beruf").",".Lang::txt("gold").",".Lang::txt("status").") VALUES('".Lang::txt("Dirty Doerthe")."', 3, '".Lang::txt("w")."', '".Lang::txt("Erntehelfer")."', 10, '".Lang::txt("boese")."');");
        $this->db->exec("INSERT INTO ".Lang::txt("bewohner")." (".Lang::txt("name").",".Lang::txt("dorfnr").",".Lang::txt("geschlecht").",".Lang::txt("beruf").",".Lang::txt("gold").",".Lang::txt("status").") VALUES('".Lang::txt("Otto Armleuchter")."', 2, '".Lang::txt("m")."', '".Lang::txt("Haendler")."', 680, '".Lang::txt("friedlich")."');");
        $this->db->exec("INSERT INTO ".Lang::txt("bewohner")." (".Lang::txt("name").",".Lang::txt("dorfnr").",".Lang::txt("geschlecht").",".Lang::txt("beruf").",".Lang::txt("gold").",".Lang::txt("status").") VALUES('".Lang::txt("Fritz Dichter")."', 3, '".Lang::txt("m")."', '".Lang::txt("Hoerbuchautor")."', 420, '".Lang::txt("friedlich")."');");
        $this->db->exec("INSERT INTO ".Lang::txt("bewohner")." (".Lang::txt("name").",".Lang::txt("dorfnr").",".Lang::txt("geschlecht").",".Lang::txt("beruf").",".Lang::txt("gold").",".Lang::txt("status").") VALUES('".Lang::txt("Enrico Zimmermann")."', 3, '".Lang::txt("m")."', '".Lang::txt("Waffenschmied")."', 510, '".Lang::txt("boese")."');");
        $this->db->exec("INSERT INTO ".Lang::txt("bewohner")." (".Lang::txt("name").",".Lang::txt("dorfnr").",".Lang::txt("geschlecht").",".Lang::txt("beruf").",".Lang::txt("gold").",".Lang::txt("status").") VALUES('".Lang::txt("Helga Rasenkopf")."', 2, '".Lang::txt("w")."', '".Lang::txt("Haendler")."', 680, '".Lang::txt("friedlich")."');");
        $this->db->exec("INSERT INTO ".Lang::txt("bewohner")." (".Lang::txt("name").",".Lang::txt("dorfnr").",".Lang::txt("geschlecht").",".Lang::txt("beruf").",".Lang::txt("gold").",".Lang::txt("status").") VALUES('".Lang::txt("Irene Hutmacher")."', 1, '".Lang::txt("w")."', '".Lang::txt("Haendler")."', 770, '".Lang::txt("boese")."');");
        $this->db->exec("INSERT INTO ".Lang::txt("bewohner")." (".Lang::txt("name").",".Lang::txt("dorfnr").",".Lang::txt("geschlecht").",".Lang::txt("beruf").",".Lang::txt("gold").",".Lang::txt("status").") VALUES('".Lang::txt("Erich Rasenkopf")."', 3, '".Lang::txt("m")."', '".Lang::txt("Metzger")."', 990, '".Lang::txt("friedlich")."');");
        $this->db->exec("INSERT INTO ".Lang::txt("bewohner")." (".Lang::txt("name").",".Lang::txt("dorfnr").",".Lang::txt("geschlecht").",".Lang::txt("beruf").",".Lang::txt("gold").",".Lang::txt("status").") VALUES('".Lang::txt("Rudolf Gaul")."', 3, '".Lang::txt("m")."', '".Lang::txt("Hufschmied")."', 390, '".Lang::txt("friedlich")."');");
        $this->db->exec("INSERT INTO ".Lang::txt("bewohner")." (".Lang::txt("name").",".Lang::txt("dorfnr").",".Lang::txt("geschlecht").",".Lang::txt("beruf").",".Lang::txt("gold").",".Lang::txt("status").") VALUES('".Lang::txt("Anna Flysh")."', 2, '".Lang::txt("w")."', '".Lang::txt("Metzger")."', 2280, '".Lang::txt("friedlich")."');");

        $this->db->exec("CREATE TABLE ".Lang::txt("gegenstand")." (".Lang::txt("gegenstand")." VARCHAR(31) PRIMARY KEY, ".Lang::txt("besitzer")." INT);");
        $this->db->exec("INSERT INTO ".Lang::txt("gegenstand")." (".Lang::txt("gegenstand").",".Lang::txt("besitzer").") VALUES ('".Lang::txt("Teekanne")."', NULL);");
        $this->db->exec("INSERT INTO ".Lang::txt("gegenstand")." (".Lang::txt("gegenstand").",".Lang::txt("besitzer").") VALUES ('".Lang::txt("Spazierstock")."', 5);");
        $this->db->exec("INSERT INTO ".Lang::txt("gegenstand")." (".Lang::txt("gegenstand").",".Lang::txt("besitzer").") VALUES ('".Lang::txt("Hammer")."', 2);");
        $this->db->exec("INSERT INTO ".Lang::txt("gegenstand")." (".Lang::txt("gegenstand").",".Lang::txt("besitzer").") VALUES ('".Lang::txt("Ring")."', NULL);");
        $this->db->exec("INSERT INTO ".Lang::txt("gegenstand")." (".Lang::txt("gegenstand").",".Lang::txt("besitzer").") VALUES ('".Lang::txt("Kaffeetasse")."', NULL);");
        $this->db->exec("INSERT INTO ".Lang::txt("gegenstand")." (".Lang::txt("gegenstand").",".Lang::txt("besitzer").") VALUES ('".Lang::txt("Eimer")."', NULL);");
        $this->db->exec("INSERT INTO ".Lang::txt("gegenstand")." (".Lang::txt("gegenstand").",".Lang::txt("besitzer").") VALUES ('".Lang::txt("Seil")."', 17);");
        $this->db->exec("INSERT INTO ".Lang::txt("gegenstand")." (".Lang::txt("gegenstand").",".Lang::txt("besitzer").") VALUES ('".Lang::txt("Pappkarton")."', NULL);");
        $this->db->exec("INSERT INTO ".Lang::txt("gegenstand")." (".Lang::txt("gegenstand").",".Lang::txt("besitzer").") VALUES ('".Lang::txt("Gluehbirne")."', NULL);");


}

private static function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}

}


class DBSimulator {
  public function DBSimulator() {}

  public function exec($query) {
    echo $query."\n";
  }
}


/*
$e = new Exercise();
$e->setSolution("SELECT gold FROM bewohner");

$db = new DB("PSeGRnn8l4");

//echo $db->isCorrect($e, "SELECT gold FROM bewohner WHERE gold > 100");

echo $db->query("SELECT gold FROM bewohner", FALSE, $e);
*/
