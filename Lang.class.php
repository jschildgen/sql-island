<?php

if(isset($_SESSION['lang'])) {
	Lang::setLanguage($_SESSION['lang']);
} else {
	if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
		$browser_lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
		Lang::setLanguage($browser_lang);
	}
}

class Lang
{
	private static $lang = null;
	private static $lock = false;
	private static $language = "de";

	static public function setLanguage($l) {
		self::$language = $l;
	}

	static public function getLanguage() {
		return self::$language;
	}

	static public function txt($orig) {

		if(self::$language == "de") {
			return $orig;
		}

    while(self::$lock) { sleep(1); }
    self::$lock = true;

		$lang_file = "lang_" . self::$language . ".csv";
		
		if(!file_exists($lang_file)) {
			$lang_file = "lang_en.csv";
		}

		if(self::$lang == null) {
			self::$lang = Array();

			foreach(explode("\n", file_get_contents($lang_file)) as $line) {
				if(strpos($line, "#") === 0) { continue; }
				@list($from, $to) = explode("|", $line);
				$from = str_replace("&#124", "|", $from);
				$from = str_replace("<br>", "\n", $from);
				$to = str_replace("&#124", "|", $to);
                                $to = str_replace("<br>", "\n", $to);
				self::$lang[$from] = $to;
			}
		}

		if(!isset(self::$lang[$orig])) {
			$orig1 = str_replace("|", "&#124;", $orig);
			$orig1 = str_replace("\n", "<br>", $orig1);
			file_put_contents($lang_file, $orig1."|\n", FILE_APPEND);
			self::$lang[$orig] = "";
		}

		self::$lock = false;
		if(isset(self::$lang[$orig]) && self::$lang[$orig]!=="") {
			return self::$lang[$orig];
		}
		return $orig;
	}
}
