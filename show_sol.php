<?php
header("Content-Type:text/plain; charset=utf-8");

require_once("Lang.class.php");
require_once("Game.class.php");

if (isset($_GET['lang'])) {
    Lang::setLanguage($_GET['lang']);
}

$game = new Game();

do {
echo $game->getExercise()->getSolution() . "\n";
} while($game->nextExercise()!==null);

