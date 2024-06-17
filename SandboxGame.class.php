<?php
require_once("Exercise.class.php");
require_once("Lang.class.php");

class Game {

	function __construct($currentExercise = 0) {
		$this->Game($currentExercise);
	}

	public function Game($currentExercise = 0) { }

	public function nextExercise() { return 0; }

	public function setPlayerName($s) { }

	public function getExercise() {
		$e = Array();

		$e[0] = new Exercise("");
		$e[0]->setSolution(null);
		$e[0]->leftimg = "";
		$e[0]->rightimg = "";
		$e[0]->speaker = "L";

		$current = $e[0];

		return $current;
	}
}
