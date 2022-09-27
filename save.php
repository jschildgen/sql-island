<?php
session_start();
header("Content-Type:text/plain; charset=utf-8");

$new_file = $_SESSION['dbID'];
$new_file .= "_";
$new_file .= $_SESSION['currentExercise'];
$new_file .= "_";
if(isset($_SESSION['lang'])) {
	$new_file .= $_SESSION['lang'];
}
$new_file .= "_";
if(isset($_SESSION['extreme'])) {
	$new_file .= "extreme";
}
$new_file .= ".sqlite";

copy("DBs/".$_SESSION['dbID'].".sqlite", "DBs/save/".$new_file);

echo $_SESSION['dbID'];