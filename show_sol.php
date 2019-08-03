<?php
header("Content-Type:text/plain; charset=utf-8");

require_once("Game.class.php");

$game = new Game();

do {
echo $game->getExercise()->getSolution() . "\n";
} while($game->nextExercise()!==null);

