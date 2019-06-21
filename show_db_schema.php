<?php
header("Content-Type:text/plain; charset=utf-8");

require_once("./DB.class.php");
require_once("Lang.class.php");

$db = new DB(DB::$MODE_SIMULATE);
