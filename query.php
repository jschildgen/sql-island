<?php
if(!isset($_SESSION)) {session_start(); header("Content-Type:text/json; charset=utf-8"); }
require_once("DB.class.php");

if(@$_SESSION['extreme'] === true) { 
  require_once("./ExtremeGame.class.php"); 
} elseif(@$_SESSION['sandbox'] === true) {
  require_once("./SandboxGame.class.php");
} else { 
  require_once("./Game.class.php"); 
}

$show_exercise = false;

if(!isset($_GET['query'])) { die(); }

$query = $_GET['query'];
$query = str_replace('\\"', '"', $query);
$query = str_replace('\\\'', '\'', $query);

if(@$query == "restart") {
	$lang = @$_SESSION['lang'];
	session_destroy();
	session_start();
	$_SESSION['lang'] = $lang;
	$show_exercise = true;
}

if(isset($_SESSION['dbID'])) {
	$db = new DB($_SESSION['dbID']);

} else {
	$db = new DB();
	$_SESSION['dbID'] = $db->getDbID();
}

if(isset($_SESSION['currentExercise'])) {
        $game = new Game($_SESSION['currentExercise']);
} else {
        $game = new Game();
}

if($query == "continue") {
	$show_exercise = true;
}
if($show_exercise) {
	if($game->getExercise() == null) {
		$result["exercise"] = Lang::txt("Das Spiel ist zu Ende.");
		$result["exercise"] .= " ".Lang::txt("Hole jetzt dein Abschluss-Zertifikat ab. Wenn du den Namen auf dem Zertifikat ändern willst, tu dies mit einem UPDATE-Befehl auf der Bewohner-Tabelle.");
		$result["certificate"] = true;
	} else {
		$game->setPlayerName($db->getPlayerName());
		$db->log("-- ".$game->getExercise()->getDescription());
		if(!isset($result)) { $result = array(); }
		$result = build_result($result, $game->getExercise());
	}
	echo json_encode($result);
	die();
}

$db->log($query);
$result = $db->query($query, FALSE, $game->getExercise());

if($result != null) {

	if($game->getExercise() != null && !$game->getExercise()->getSolved()) {
		if($result["code"]>0) {

			if($game->getExercise()->answer != null) { $result["answer"] = $game->getExercise()->answer; }

			$_SESSION['currentExercise'] = $game->nextExercise();
			//echo ' <a href="javascript:query(\'continue\');">Weiter</a>';
			if($game->getExercise() == null) {
				$result["exercise"] = Lang::txt("Das Spiel ist zu Ende.");
			} else {
				$game->setPlayerName($db->getPlayerName());
				$db->log("-- ".$game->getExercise()->getDescription());
				$result = build_result($result, $game->getExercise());
			}
		}
	} elseif($game->getExercise() != null) {
		$result["solved"] = "solved";
		$_SESSION['currentExercise'] = $game->nextExercise();
	}

	echo json_encode($result);
}

function build_result($res, $e) {
	$result = $res;
	$result["exercise"] = $e->getDescription();
	if($e->description2 != null) { $result["description2"] = $e->description2; }
//	if($e->answer != null) { $result["answer"] = $e->answer; }
	if($e->speaker != null) { $result["speaker"] = $e->speaker; }
	if($e->speaker2 != null) { $result["speaker2"] = $e->speaker2; }
	if($e->leftimg != null) { $result["leftimg"] = $e->leftimg; }
	if($e->rightimg != null) { $result["rightimg"] = $e->rightimg; }
	if($e->getSolved()) {
		$result["solved"] = "solved";
		$result["query"] = $e->getSolution();
	}
	return $result;
}
