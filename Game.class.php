<?php
require_once("Exercise.class.php");
require_once("Lang.class.php");

class Game {
	private $currentExercise;
	private $player_name;

	public function Game($currentExercise = 0) { $this->currentExercise = $currentExercise; }

	public function nextExercise() { $this->currentExercise++; return $this->currentExercise; }

	public function setPlayerName($s) { $this->player_name = $s; }

	public function getExercise() {
		$e = Array();

		$e[0] = new Exercise(Lang::txt("Hui, was ist passiert? Es scheint, als habe ich als einziger den Flugzeugabsturz überlebt. Gut, dass ich auf dieser Insel gelandet bin. Hier gibt es ja sogar ein paar Dörfer."));
		$e[0]->setSolution("SELECT * FROM ".Lang::txt("dorf"));
		$e[0]->setSolved(true);
		$e[0]->leftimg = "avatar";
		$e[0]->rightimg = "";
		$e[0]->speaker = "L";

		$e[1] = new Exercise(Lang::txt("Und jede Menge Bewohner gibt es hier auch. Zeige mir die Liste der Bewohner."));
		$e[1]->setSolution("SELECT * FROM ".Lang::txt("bewohner"));
		$e[1]->answer = Lang::txt("Wow, hier ist einiges los!");
		$e[1]->leftimg = "avatar_lachend";
		$e[1]->rightimg = "";
		$e[1]->speaker = "L";

		$e[2] = new Exercise(Lang::txt("Mensch, was bin ich hungrig. Ich suche mir erst einmal einen Metzger, bei dem ich eine Scheibe Wurst schnorren kann."));
		$e[2]->setSolution("SELECT * FROM ".Lang::txt("bewohner")." WHERE ".Lang::txt("beruf")." = '".Lang::txt("Metzger")."'");
		$e[2]->setSolved(true);
		$e[2]->leftimg = "avatar";
		$e[2]->rightimg = "";
		$e[2]->speaker = "L";


		$e[3] = new Exercise(Lang::txt("Hier, lass es dir schmecken! Und pass bei deiner Reise gut auf, dass du dich von bösen Bewohnern fern hälst, solange du unbewaffnet bist. Denn nicht jeder hier ist friedlich!"));
		$e[3]->description2 = Lang::txt("Danke Erich! Nagut, dann muss ich mal schauen, welche Bewohner friedlich sind.");
		$e[3]->setSolution("SELECT * FROM ".Lang::txt("bewohner")." WHERE ".Lang::txt("status")." = '".Lang::txt("friedlich")."'");
		$e[3]->leftimg = "avatar_wurst";
		$e[3]->rightimg = "erich";
		$e[3]->speaker = "R";
		$e[3]->speaker2 = "L";


		$e[4] = new Exercise(Lang::txt("Früher oder später brauche ich aber ein Schwert. Lasst uns einen friedlichen Waffenschmied suchen, der mir ein Schwert schmieden kann. (Hinweis: Bedingungen im WHERE-Teil kannst du mit AND verknüpfen)"));
		$e[4]->setSolution("SELECT * FROM ".Lang::txt("bewohner")." WHERE ".Lang::txt("beruf")." = '".Lang::txt("Waffenschmied")."' AND ".Lang::txt("status")." = '".Lang::txt("friedlich")."'");
		$e[4]->leftimg = "avatar";
		$e[4]->rightimg = "";
		$e[4]->speaker = "L";

		$e[5] = new Exercise(Lang::txt("Hm, das sind sehr wenige. Vielleicht gibt es noch andere friedliche Schmiede, z.B. Hufschmied, Schmied, Waffenschmied, etc. Probiere beruf LIKE '%schmied', um alle Bewohner zu finden, deren Beruf mit 'schmied' endet (% ist ein Platzhalter für beliebig viele Zeichen)."));
		$e[5]->setSolution("SELECT * FROM ".Lang::txt("bewohner")." WHERE ".Lang::txt("beruf")." LIKE '%".Lang::txt("schmied")."' AND ".Lang::txt("status")." = '".Lang::txt("friedlich")."'");
		$e[5]->answer = Lang::txt("Das sieht schon besser aus! Diese Schmiede kommen alle in Frage. Ich gehe sie dann mal nacheinander besuchen.");
		$e[5]->leftimg = "avatar_ueberrascht";
		$e[5]->rightimg = "";
		$e[5]->speaker = "L";

		$e[6] = new Exercise(Lang::txt("Hallo Fremder, wohin des Wegs? Ich bin Paul, der Bürgermeister von Affenstadt. Ich trage dich gerne als Bewohner meines Dorfes ein."));
		$e[6]->setSolution("INSERT INTO ".Lang::txt("bewohner")." (".Lang::txt("name").", ".Lang::txt("dorfnr").", ".Lang::txt("geschlecht").", ".Lang::txt("beruf").", ".Lang::txt("gold").", ".Lang::txt("status").") VALUES ('".Lang::txt("Fremder")."', 1, '?', '?', 0, '?')");
		$e[6]->setSolved(true);
		$e[6]->leftimg = "avatar_freuend";
		$e[6]->rightimg = "paul";
		$e[6]->speaker = "R";

		$e[7] = new Exercise(Lang::txt("Hey, nenn mich doch nicht Fremder! Naja, egal. Wie ist eigentlich meine bewohnernr? (Tipp: Der * in den vorherigen Abfragen stand immer für 'alle Spalten'. Stattdessen kannst du aber auch einen oder mehrere mit Komma getrennte Spaltennamen angeben."));
		$e[7]->setSolution("SELECT ".Lang::txt("bewohnernr")." FROM ".Lang::txt("bewohner")." WHERE ".Lang::txt("name")." = '".Lang::txt("Fremder")."'");
		$e[7]->leftimg = "avatar_ueberrascht";
		$e[7]->rightimg = "paul";
		$e[7]->speaker = "L";

		$e[8] = new Exercise(Lang::txt("Hallo Ernst! Was kostet bei dir ein Schwert?"));
		$e[8]->description2 = Lang::txt("Ich schmiede dir ein Schwert für nur 150 Gold. Billiger bekommst du es nirgendwo! Wie viel Gold hast du momentan?");
		$e[8]->setSolution("SELECT ".Lang::txt("gold")." FROM ".Lang::txt("bewohner")." WHERE ".Lang::txt("bewohnernr")." = 20");
		$e[8]->leftimg = "avatar";
		$e[8]->rightimg = "ernst";
		$e[8]->speaker = "L";
		$e[8]->speaker2 = "R";

		$e[9] = new Exercise(Lang::txt("Mist, ich habe ja noch gar kein Gold. Ich habe aber auch keine Lust dafür arbeiten zu gehen. Hmmm, vorhin habe ich viele Gegenstände herumliegen gesehen, die niemandem gehören. Diese Gegenstände könnte ich einsammeln und an Händler verkaufen. Liste alle Gegenstände auf, die niemandem gehören. Tipp: Herrenlose Gegenstände erkennt man an WHERE besitzer IS NULL."));
		$e[9]->setSolution("SELECT * FROM ".Lang::txt("gegenstand")." WHERE ".Lang::txt("besitzer")." IS NULL");
		$e[9]->answer = Lang::txt("So viele tolle Sachen!");
		$e[9]->leftimg = "avatar_wuetend";
		$e[9]->rightimg = "";
		$e[9]->speaker = "L";

		$e[10] = new Exercise(Lang::txt("Lasst uns die Kaffeetasse einsammeln. Eine Kaffeetasse kann man immer mal gebrauchen."));
		$e[10]->setSolution("UPDATE ".Lang::txt("gegenstand")." SET ".Lang::txt("besitzer")." = 20 WHERE ".Lang::txt("gegenstand")." = '".Lang::txt("Kaffeetasse")."'");
		$e[10]->setSolved(true);
		$e[10]->leftimg = "avatar_freuend";
		$e[10]->rightimg = "items";
		$e[10]->speaker = "L";

		$e[11] = new Exercise(Lang::txt("Kennst du einen Trick, wie wir alle Gegenstände auf einmal einsammeln können, die niemandem gehören?"));
		$e[11]->setSolution("UPDATE ".Lang::txt("gegenstand")." SET ".Lang::txt("besitzer")." = 20 WHERE ".Lang::txt("besitzer")." IS NULL");
		$e[11]->setVerificationQuery("SELECT * FROM ".Lang::txt("gegenstand")." WHERE ".Lang::txt("besitzer")." = 20");
		$e[11]->setVerificationCount(6);
		$e[11]->leftimg = "avatar_freuend";
		$e[11]->rightimg = "items";
		$e[11]->speaker = "L";

		$e[12] = new Exercise(Lang::txt("Jawoll! Welche Gegenstände besitze ich nun?"));
    $e[12]->setSolution("SELECT * FROM ".Lang::txt("gegenstand")." WHERE ".Lang::txt("besitzer")." = 20");
		$e[12]->leftimg = "avatar";
		$e[12]->rightimg = "items";
		$e[12]->speaker = "L";

		$e[13] = new Exercise(Lang::txt("Finde friedliche Bewohner mit dem Beruf Haendler oder Kaufmann. Eventuell möchten sie etwas von uns kaufen. (Hinweis: Achte bei AND- und OR-Verknüpfungen auf korrekte Klammerung)"));
		$e[13]->setSolution("SELECT * FROM ".Lang::txt("bewohner")." WHERE (".Lang::txt("beruf")." = '".Lang::txt("Haendler")."' OR ".Lang::txt("beruf")." = '".Lang::txt("Kaufmann")."') AND ".Lang::txt("status")." = '".Lang::txt("friedlich")."'");
		$e[13]->leftimg = "avatar";
		$e[13]->rightimg = "";
		$e[13]->speaker = "L";

		$e[14] = new Exercise(Lang::txt("Ich interessiere mich für den Ring und die Teekanne. Der Rest ist alles Schrott. Gib mir bitte die beiden Gegenstände. Meine Bewohnernummer ist übrigens 15."));
		$e[14]->setSolution("UPDATE ".Lang::txt("gegenstand")." SET ".Lang::txt("besitzer")." = 15 WHERE ".Lang::txt("gegenstand")." = '".Lang::txt("Teekanne")."' OR ".Lang::txt("gegenstand")." = '".Lang::txt("Ring")."'");
		$e[14]->setVerificationQuery("SELECT * FROM ".Lang::txt("gegenstand")." WHERE ".Lang::txt("besitzer")." = 15 AND (".Lang::txt("gegenstand")." = '".Lang::txt("Teekanne")."' OR ".Lang::txt("gegenstand")." = '".Lang::txt("Ring")."')");
		$e[14]->setVerificationCount(2);
		$e[14]->answer = Lang::txt("Dankeschön!");
		$e[14]->leftimg = "avatar";
		$e[14]->rightimg = "helga";
		$e[14]->speaker = "R";

		$e[15] = new Exercise(Lang::txt("Hier hast du einen Haufen Gold!"));
		$e[15]->setSolution("UPDATE ".Lang::txt("bewohner")." SET ".Lang::txt("gold")." = ".Lang::txt("gold")." + 120 WHERE ".Lang::txt("bewohnernr")." = 20");
		$e[15]->setSolved(true);
		$e[15]->leftimg = "avatar_freuend";
		$e[15]->rightimg = "helga_geld";
		$e[15]->speaker = "R";

		$e[16] = new Exercise(Lang::txt("Leider reicht das noch nicht für ein Schwert. Dann muss ich wohl doch arbeiten. Bevor ich mich jedoch irgendwo bewerbe, sollte ich vielleicht meinen Namen von Fremder auf meinen richtigen Namen ändern, ansonsten wird mich niemand einstellen."));
		$e[16]->setSolution("UPDATE ".Lang::txt("bewohner")." SET ".Lang::txt("name")." = 'Peter' WHERE ".Lang::txt("bewohnernr")." = 20");
		$e[16]->setVerificationQuery("SELECT * FROM ".Lang::txt("bewohner")." WHERE ".Lang::txt("name")." = '".Lang::txt("Fremder")."'");
		$e[16]->setVerificationCount(0);
		$e[16]->leftimg = "avatar_wuetend";
		$e[16]->rightimg = "";
		$e[16]->speaker = "L";

		$e[17] = new Exercise(Lang::txt("In meiner Freizeit backe ich gerne. Ich glaube, ich verdiene mir ein bisschen Geld als Bäcker. Zeige mir alle Bäcker. Tipp: Mit ORDER BY gold kannst du die Liste sortieren, mit ORDER BY gold DESC steht sogar der reichste Bäcker oben."));
		$e[17]->setSolution("SELECT * FROM ".Lang::txt("bewohner")." WHERE ".Lang::txt("beruf")." = '".Lang::txt("Baecker")."' ORDER BY ".Lang::txt("gold")." DESC");
		$e[17]->answer = Lang::txt("Ach, der Paul! Den kenn ich doch!");
		$e[17]->leftimg = "avatar";
		$e[17]->rightimg = "";
		$e[17]->speaker = "L";

		$e[18] = new Exercise(Lang::txt("Hi, da bist du ja wieder! Soso, %%%PLAYER_NAME%%% heißt du also. Und du willst als Bäcker arbeiten? Da sag ich nicht nein. Ich zahle dir pro hundert Brötchen, die du mir bäckst, 1 Gold."));
		$e[18]->description2 = Lang::txt("(8 Stunden später...) Hier bittesehr, zehntausend Brötchen! Ich kündige! Ich habe nun genug Gold, um mir ein Schwert zu kaufen! Mal schauen, was jetzt mit meinem Kontostand passiert.");
		$e[18]->setSolution("UPDATE ".Lang::txt("bewohner")." SET ".Lang::txt("gold")." = ".Lang::txt("gold")." + 100 - 150 WHERE ".Lang::txt("bewohnernr")." = 20");
		$e[18]->setSolved(true);
		$e[18]->leftimg = "avatar_winkend";
		$e[18]->rightimg = "paul_baecker";
		$e[18]->speaker = "R";
		$e[18]->speaker2 = "L";

		$e[19] = new Exercise(Lang::txt("Hier ist dein neues Schwert, %%%PLAYER_NAME_FUNNY%%%! Nun kannst du überall hin!"));
		$e[19]->description2 = Lang::txt("Ich heiße %%%PLAYER_NAME%%%! Aber trotzdem danke.");
		$e[19]->setSolution("INSERT INTO ".Lang::txt("gegenstand")." (".Lang::txt("gegenstand").", ".Lang::txt("besitzer").") VALUES ('".Lang::txt("Schwert")."', 20)");
		$e[19]->setSolved(true);
		$e[19]->leftimg = "avatar_schwert_freuend";
		$e[19]->rightimg = "ernst";
		$e[19]->speaker = "R";
		$e[19]->speaker2 = "L";

		$e[20] = new Exercise(Lang::txt("Gibt es auf der Insel einen Piloten? Er könnte mich nach Hause fliegen."));
		$e[20]->setSolution("SELECT * FROM ".Lang::txt("bewohner")." WHERE ".Lang::txt("beruf")." = '".Lang::txt("Pilot")."'");
		$e[20]->answer = Lang::txt("Oh, er hat den Status 'gefangen'.");
		$e[20]->leftimg = "avatar_schwert";
		$e[20]->rightimg = "ernst";
		$e[20]->speaker = "L";

		$e[21] = new Exercise(Lang::txt("Es ist schrecklich! Dirty Dieter hält den Piloten gefangen! Ich verrate dir einen Trick, wie wir schnell herausfinden können, in welchem Dorf Dirty Dieter wohnt."));
		$e[21]->setSolution("SELECT ".Lang::txt("dorf").".".Lang::txt("name")." FROM ".Lang::txt("dorf").", ".Lang::txt("bewohner")." WHERE ".Lang::txt("dorf").".".Lang::txt("dorfnr")." = ".Lang::txt("bewohner").".".Lang::txt("dorfnr")." AND ".Lang::txt("bewohner").".".Lang::txt("name")." = '".Lang::txt("Dirty Dieter")."'");
		$e[21]->setSolved(true);
		$e[21]->leftimg = "avatar_schwert";
		$e[21]->rightimg = "ernst";
		$e[21]->speaker = "R";

		$e[22] = new Exercise(Lang::txt("Auf diese Weise kannst du das Dorf mit der Dorf-Nummer suchen, die bei Dirty Dieter im Feld dorfnr steht. Ein solch genialer Ausdruck nennt sich Verbund oder Join."));
		$e[22]->description2 = Lang::txt("Danke für den Tipp. Dann suche ich erst einmal den Häuptling des Dorfes Zwiebelhausen. Im Feld haeuptling der Tabelle dorf steht ja die bewohnernr des Häuptlings des jeweiligen Dorfes.");
		$e[22]->setSolution("SELECT ".Lang::txt("bewohner").".".Lang::txt("name")." FROM ".Lang::txt("dorf").", ".Lang::txt("bewohner")." WHERE ".Lang::txt("dorf").".".Lang::txt("haeuptling")." = ".Lang::txt("bewohner").".".Lang::txt("bewohnernr")." AND ".Lang::txt("dorf").".".Lang::txt("name")." = '".Lang::txt("Zwiebelhausen")."'");
		$e[22]->answer = Lang::txt("Juhu! Ich hab's! Dann gehe ich mal Fritz besuchen, um ihn nach Dirty Dieter und dem Piloten zu fragen.");
		$e[22]->leftimg = "avatar_schwert_freuend";
		$e[22]->rightimg = "ernst";
		$e[22]->speaker = "R";
		$e[22]->speaker2 = "L";

		$e[23] = new Exercise(Lang::txt("Hm, wie viele Einwohner hat eigentlich Zwiebelhausen?"));
		$e[23]->setSolution("SELECT COUNT(*) FROM ".Lang::txt("bewohner").", ".Lang::txt("dorf")." WHERE ".Lang::txt("dorf").".".Lang::txt("dorfnr")." = ".Lang::txt("bewohner").".".Lang::txt("dorfnr")." AND ".Lang::txt("dorf").".".Lang::txt("name")." = '".Lang::txt("Zwiebelhausen")."'");
		$e[23]->setSolved(true);
		$e[23]->leftimg = "avatar_schwert";
		$e[23]->rightimg = "";
		$e[23]->speaker = "L";

		$e[24] = new Exercise(Lang::txt("Hallo %%%PLAYER_NAME%%%, Dirty Dieter hält den Piloten im Haus seiner Schwester gefangen. Soll ich dir verraten, wie viele Frauen es in Zwiebelhausen gibt? Ach was, das kannst du schon selbst herausfinden! (Hinweis: Frauen erkennt man an geschlecht = 'w')"));
		$e[24]->setSolution("SELECT COUNT(*) FROM ".Lang::txt("bewohner").", ".Lang::txt("dorf")." WHERE ".Lang::txt("dorf").".".Lang::txt("dorfnr")." = ".Lang::txt("bewohner").".".Lang::txt("dorfnr")." AND ".Lang::txt("dorf").".".Lang::txt("name")." = '".Lang::txt("Zwiebelhausen")."' AND ".Lang::txt("bewohner").".".Lang::txt("geschlecht")." = '".Lang::txt("w")."'");
		$e[24]->leftimg = "avatar_schwert";
		$e[24]->rightimg = "fritz";
		$e[24]->speaker = "R";

		$e[25] = new Exercise(Lang::txt("Ha, nur eine Frau. Mal schauen, wie sie heißt."));
		$e[25]->setSolution("SELECT ".Lang::txt("bewohner").".".Lang::txt("name")." FROM ".Lang::txt("bewohner").", ".Lang::txt("dorf")." WHERE ".Lang::txt("dorf").".".Lang::txt("dorfnr")." = ".Lang::txt("bewohner").".".Lang::txt("dorfnr")." AND ".Lang::txt("dorf").".".Lang::txt("name")." = '".Lang::txt("Zwiebelhausen")."' AND ".Lang::txt("bewohner").".".Lang::txt("geschlecht")." = '".Lang::txt("w")."'");
		$e[25]->answer = Lang::txt("So! Dann gehe ich jetzt da hin!");
		$e[25]->leftimg = "avatar_schwert_freuend";
		$e[25]->rightimg = "fritz";
		$e[25]->speaker = "L";

		$e[26] = new Exercise(Lang::txt("%%%PLAYER_NAME%%%, gib mir alles Gold, was die Bewohner von unserem Nachbardorf Gurkendorf zusammen besitzen und ich lasse den Piloten frei! Du willst wissen, wie viel das ist? Ich zeige es dir!"));
		$e[26]->setSolution("SELECT SUM(".Lang::txt("bewohner").".".Lang::txt("gold").") FROM ".Lang::txt("bewohner").", ".Lang::txt("dorf")." WHERE ".Lang::txt("dorf").".".Lang::txt("dorfnr")." = ".Lang::txt("bewohner").".".Lang::txt("dorfnr")." AND ".Lang::txt("dorf").".".Lang::txt("name")." = '".Lang::txt("Gurkendorf")."'");
		$e[26]->setSolved(true);
		$e[26]->leftimg = "avatar_schwert";
		$e[26]->rightimg = "dieter";
		$e[26]->speaker = "R";

		$e[27] = new Exercise(Lang::txt("So viel Gold werde ich niemals allein durch Brötchenbacken verdienen können. Ich muss mir etwas anderes einfallen lassen. Wenn ich Gegenstände verkaufe und zusätzlich noch als Bäcker arbeite, kann ich maximal so viel Gold bekommen, wie die Händler, Kaufmänner und Bäcker zusammen besitzen. Wie viel ist das?"));
		$e[27]->setSolution("SELECT SUM(".Lang::txt("bewohner").".".Lang::txt("gold").") FROM ".Lang::txt("bewohner")." WHERE ".Lang::txt("beruf")." = '".Lang::txt("Haendler")."' OR ".Lang::txt("beruf")." = '".Lang::txt("Kaufmann")."' OR ".Lang::txt("beruf")." = '".Lang::txt("Baecker")."'");
		$e[27]->answer = Lang::txt("Das ist viel zu wenig.");
		$e[27]->leftimg = "avatar_schwert";
		$e[27]->rightimg = "dieter";
		$e[27]->speaker = "L";

		$e[28] = new Exercise(Lang::txt("Schauen wir mal die gesamten sowie durchschnittlichen Goldvorräte der einzelnen Berufe an."));
		$e[28]->setSolution("SELECT ".Lang::txt("beruf").", SUM(".Lang::txt("bewohner").".".Lang::txt("gold")."), AVG(".Lang::txt("bewohner").".".Lang::txt("gold").") FROM ".Lang::txt("bewohner")." GROUP BY ".Lang::txt("beruf")." ORDER BY AVG(".Lang::txt("bewohner").".".Lang::txt("gold").")");
		$e[28]->setSolved(true);
		$e[28]->leftimg = "avatar_schwert";
		$e[28]->rightimg = "dieter";
		$e[28]->speaker = "L";

		$e[29] = new Exercise(Lang::txt("Interessant, die Metzger haben also das meiste Gold. Warum auch immer... Wie viel Gold haben im Durchschnitt die einzelnen Bewohnergruppen je nach Status (friedlich, böse, gefangen)?"));
		$e[29]->setSolution("SELECT ".Lang::txt("status").", AVG(".Lang::txt("bewohner").".".Lang::txt("gold").") FROM ".Lang::txt("bewohner")." GROUP BY ".Lang::txt("status")."");
		$e[29]->answer = Lang::txt("Okay, ich muss also die bösen Bewohner ausrauben.");
		$e[29]->leftimg = "avatar_schwert";
		$e[29]->rightimg = "dieter";
		$e[29]->speaker = "L";

		$e[30] = new Exercise(Lang::txt("Dann kann ich auch gleich Dirty Dieter mit dem Schwert töten und den Piloten befreien."));
		$e[30]->setSolution("DELETE FROM ".Lang::txt("bewohner")." WHERE ".Lang::txt("name")." = '".Lang::txt("Dirty Dieter")."'");
		$e[30]->setSolved(true);
		$e[30]->leftimg = "avatar_schwert_sehr_wuetend";
		$e[30]->rightimg = "dieter";
		$e[30]->speaker = "L";

		$e[31] = new Exercise(Lang::txt("Heeeey! Jetzt bin ich aber sauer! Was tust du wohl als nächstes, %%%PLAYER_NAME%%%?"));
		$e[31]->setSolution("DELETE FROM ".Lang::txt("bewohner")." WHERE ".Lang::txt("name")." = 'Dirty Dörthe'");
		$e[31]->setVerificationQuery("SELECT * FROM ".Lang::txt("bewohner")." WHERE ".Lang::txt("name")." = '".Lang::txt("Dirty Doerthe")."'");
		$e[31]->setVerificationCount(0);
		$e[31]->leftimg = "avatar_schwert_sehr_wuetend";
		$e[31]->rightimg = "doerthe";
		$e[31]->speaker = "R";

		$e[32] = new Exercise(Lang::txt("Yeah! Jetzt muss ich nur noch den Piloten befreien."));
		$e[32]->setSolution("UPDATE ".Lang::txt("bewohner")." SET ".Lang::txt("status")." = '".Lang::txt("friedlich")."' WHERE ".Lang::txt("beruf")." = '".Lang::txt("Pilot")."'");
		$e[32]->setVerificationQuery("SELECT * FROM ".Lang::txt("bewohner")." WHERE ".Lang::txt("status")." = '".Lang::txt("friedlich")."' AND ".Lang::txt("beruf")." = '".Lang::txt("Pilot")."'");
		$e[32]->setVerificationCount(1);
		$e[32]->leftimg = "avatar_schwert_freuend";
		$e[32]->rightimg = "arthur";
		$e[32]->speaker = "L";

		$e[33] = new Exercise(Lang::txt("Vielen vielen Dank, %%%PLAYER_NAME%%%! Jetzt fliege ich dich nach Hause."));
		$e[33]->description2 = Lang::txt("Juhu! Und als Andenken nehme ich mein Schwert, ein bisschen Gold und die nutzlosen Gegenstände mit nach Hause. Was für ein Abenteuer!");
		$e[33]->setSolution("UPDATE ".Lang::txt("bewohner")." SET ".Lang::txt("status")." = '".Lang::txt("ausgewandert")."' WHERE ".Lang::txt("bewohnernr")." = 20");
		$e[33]->setSolved(true);
		$e[33]->leftimg = "avatar_schwert_freuend";
		$e[33]->rightimg = "arthur_frei";
		$e[33]->speaker = "R";
		$e[33]->speaker2 = "L";

		//$e = array($e[6], $e[16]);

		if($this->currentExercise >= count($e)) {
			return null;
		}

		$current = $e[$this->currentExercise];

		$current->description = $this->replace_player_name($current->description);
		$current->description2 = $this->replace_player_name($current->description2);
		$current->answer = $this->replace_player_name($current->answer);


		return $current;
	}

	private function replace_player_name($str) {
		if($str == null) { return null; }
		$s = $str;
		if($this->player_name != null) {
			$s = str_replace("%%%PLAYER_NAME%%%", $this->player_name, $s);
			$s = str_replace("%%%PLAYER_NAME_FUNNY%%%", preg_replace('/[aeiou]/','o', $this->player_name), $s);
		}
		return $s;
	}
}
