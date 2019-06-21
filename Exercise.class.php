<?php

class Exercise {

	public $description;
	private $hint;
	private $solved = false;
	private $updates = false;
	private $solution;
	private $verificationQuery;
	private $verificationCount;
	public $answer;
	public $description2;
	public $speaker;
	public $speaker2;
	public $leftimg;
	public $rightimg;

	public function Exercise($description = "") { $this->description = $description; }

	public function getDescription() {	return $this->description ;	}
	public function getDescription2() { return $this->description2; 	}

	public function getHint() { return $this->hint; }
	public function getSolved() { return $this->solved; }
	public function getUpdates() { return $this->updates; }
	public function getSolution() { return $this->solution; }
	public function getVerificationQuery() { return $this->verificationQuery; }
	public function getVerificationCount() { return $this->verificationCount; }
	public function setDescription($s) { $this->description = $s; }
	public function setHint($s) { $this->hint = $s; }
	public function setSolved($b) { $this->solved = $b; }
	public function setUpdates($b) { $this->updates = $b; }
	public function setSolution($s) { $this->solution = $s; }
	public function setVerificationQuery($s) { $this->verificationQuery = $s; $this->updates = true; }
	public function setVerificationCount($c) { $this->verificationCount = $c; }

	public function printExercise($player_name = null, $exercise_id = null) {
		$description = $this->description;
		$description = str_replace("\n", "<br>", $description);

		if($player_name != null) {
			$description = str_replace("%%%PLAYER_NAME%%%", $player_name, $description);
		}

		echo "<p style=\"color:darkblue;\" id=\"exercise".$exercise_id."\"><b><i>".$description."</i></b></p>";

		if($this->solved) {
			$_GET['query'] = $this->solution;
			require("query.php");
		}
	}

}
