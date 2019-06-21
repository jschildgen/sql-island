<?php
require_once("Exercise.class.php");
require_once("Lang.class.php");

class Game {
	private $currentExercise;

	public function Game($currentExercise = 0) { $this->currentExercise = $currentExercise; }

	public function nextExercise() { $this->currentExercise++; return $this->currentExercise; }

	public function getExercise() { 
		$e = Array();

		$e[0] = new Exercise(Lang::txt("Nach einem Flugzeugabsturz stellst du fest, dass du der einzige Überlebende bist. Du landest auf einer Insel, erkundest sie und findest einige Dörfer:"));
		$e[0]->setSolution("SELECT * FROM ".Lang::txt("dorf"));
		$e[0]->setSolved(true);

		$e[1] = new Exercise(Lang::txt("Du triffst während deiner Erkundung auf viele Bewohner der Insel. Welche?"));
		$e[1]->setSolution("SELECT * FROM ".Lang::txt("bewohner"));

		$e[2] = new Exercise(Lang::txt("Deine Erkundung hat dich hungrig gemacht. Lass uns eine Metzgerei suchen und eine Gratis-Wurstscheibe schnorren!"));
		$e[2]->setSolution("SELECT * FROM ".Lang::txt("bewohner")." WHERE ".Lang::txt("beruf")." = '".Lang::txt("Metzger")."'");
		$e[2]->setSolved(true);

		$e[3] = new Exercise(Lang::txt("Juhu, Erich gibt uns eine Scheibe Wurst! Bei deiner weiteren Reise solltest du jedoch beachten, dass nicht alle Bewohner friedlich sind. Da du nicht bewaffnet bist, solltest du dich zunächst von den bösen Bewohnern fernhalten. Welche Bewohner sind friedlich?"));
		$e[3]->setSolution("SELECT * FROM ".Lang::txt("bewohner")." WHERE ".Lang::txt("status")." = '".Lang::txt("friedlich")."'");

		$e[4] = new Exercise(Lang::txt("Sehr gut! Nun suche dir einen friedlichen Waffenschmied, der dir ein Schwert schmieden kann. (Hinweis: Bedingungen im WHERE-Teil kannst du mit AND verknüpfen)"));
		$e[4]->setSolution("SELECT * FROM ".Lang::txt("bewohner")." WHERE ".Lang::txt("beruf")." = '".Lang::txt("Waffenschmied")."' AND ".Lang::txt("status")." = '".Lang::txt("friedlich")."'");

		$e[5] = new Exercise(Lang::txt("Hm, das sind sehr wenige. Vielleicht gibt es noch andere Schmiede, z.B. Hufschmied, Schmied, Waffenschmied, etc. Probiere beruf LIKE '%schmied', um alle Bewohner zu finden, deren Beruf mit 'schmied' endet (% ist ein Platzhalter für beliebig viele Zeichen)."));
		$e[5]->setSolution("SELECT * FROM ".Lang::txt("bewohner")." WHERE ".Lang::txt("beruf")." LIKE '%".Lang::txt("schmied")."' AND ".Lang::txt("status")." = '".Lang::txt("friedlich")."'");

		$e[6] = new Exercise(Lang::txt("Sehr gut! Diese Schmiede kommen alle in Frage. Du gehst sie nacheinader besuchen, um sie zu fragen, ob sie dir ein Schwert schmieden können.\nWährend deiner Erkundungstour triffst du auf Paul, einen Bürgermeister. Er trägt dich als Bewohner in sein Dorf ein. Da er dich nicht nach deinem Namen gefragt hat, nennt er dich &quot;Fremder&quot;."));
		$e[6]->setSolution("INSERT INTO ".Lang::txt("bewohner")." (".Lang::txt("name").", ".Lang::txt("dorfnr").", ".Lang::txt("geschlecht").", ".Lang::txt("beruf").", ".Lang::txt("gold").", ".Lang::txt("status").") VALUES ('".Lang::txt("Fremder")."', 2, '?', '?', 0, '?')");
		$e[6]->setSolved(true);

		$e[7] = new Exercise(Lang::txt("Finde deine bewohnernr heraus! (Tipp: Der * in den vorherigen Abfragen stand immer für &quot;alle Spalten&quot;. Stattdessen kannst du aber auch einen oder mehrere mit Komma getrennte Spaltennamen angeben."));
		$e[7]->setSolution("SELECT ".Lang::txt("bewohnernr")." FROM ".Lang::txt("bewohner")." WHERE ".Lang::txt("name")." = '".Lang::txt("Fremder")."'");

		$e[8] = new Exercise(Lang::txt("Nun besuchst du einen der Schmiede, die du dir rausgesucht hast. Er sagt dir, dass ein Schwert 150 Gold kostet. Wie viel Gold hast du momentan?"));
		$e[8]->setSolution("SELECT ".Lang::txt("gold")." FROM ".Lang::txt("bewohner")." WHERE ".Lang::txt("bewohnernr")." = 20");

		$e[9] = new Exercise(Lang::txt("Mist! Um an Gold zu kommen, müsstest du arbeiten... Obwohl! Man erzählt sich, dass auf der Insel viele Gegenstände herumliegen, die niemandem gehören. Diese Gegenstände kannst du einsammeln und an Händler verkaufen. Liste alle Gegenstände auf, die niemandem gehören. Tipp: Herrenlose Gegenstände erkennt man an WHERE besitzer IS NULL."));
		$e[9]->setSolution("SELECT * FROM ".Lang::txt("gegenstand")." WHERE ".Lang::txt("besitzer")." IS NULL");

		$e[10] = new Exercise(Lang::txt("Lasst uns die Kaffeetasse einsammeln. Eine Kaffeetasse kann man immer mal gebrauchen."));
		$e[10]->setSolution("UPDATE ".Lang::txt("gegenstand")." SET ".Lang::txt("besitzer")." = 20 WHERE ".Lang::txt("gegenstand")." = 'Kaffeetasse'");
		$e[10]->setSolved(true);

		$e[11] = new Exercise(Lang::txt("Kennst du einen Trick, wie wir alle Gegenstände auf einmal einsammeln können, die niemandem gehören?"));
		$e[11]->setSolution("UPDATE ".Lang::txt("gegenstand")." SET ".Lang::txt("besitzer")." = 20 WHERE ".Lang::txt("besitzer")." IS NULL");
		$e[11]->setVerificationQuery("SELECT * FROM ".Lang::txt("gegenstand")." WHERE ".Lang::txt("besitzer")." = 20");
		$e[11]->setVerificationCount(6);

		$e[12] = new Exercise(Lang::txt("Yeah! Zeige mir nun alle Gegenstände, die wir haben!"));
                $e[12]->setSolution("SELECT * FROM ".Lang::txt("gegenstand")." WHERE ".Lang::txt("besitzer")." = 20");

		$e[13] = new Exercise(Lang::txt("Finde friedliche Bewohner mit dem Beruf Haendler oder Kaufmann. Eventuell möchten sie etwas von uns kaufen. (Hinweis: Achte bei AND- und OR-Verknüpfungen auf korrekte Klammerung)"));
		$e[13]->setSolution("SELECT * FROM ".Lang::txt("bewohner")." WHERE (".Lang::txt("beruf")." = '".Lang::txt("Haendler")."' OR ".Lang::txt("beruf")." = '".Lang::txt("Kaufmann")."') AND ".Lang::txt("status")." = '".Lang::txt("friedlich")."'");

		$e[14] = new Exercise(Lang::txt("Du besuchst alle diese Händler und Kaufleute. Sie finden, du besitzt nur Schrott. Lediglich Helga Rasenkopf mit der Bewohnernummer 15 ist an dem Ring und der Teekanne interessiert. Gib ihr die beiden Gegenstände!"));
		$e[14]->setSolution("UPDATE ".Lang::txt("gegenstand")." SET ".Lang::txt("besitzer")." = 15 WHERE ".Lang::txt("gegenstand")." = '".Lang::txt("Teekanne")."' OR ".Lang::txt("gegenstand")." = '".Lang::txt("Ring")."'");
		$e[14]->setVerificationQuery("SELECT * FROM ".Lang::txt("gegenstand")." WHERE ".Lang::txt("besitzer")." = 15 AND (".Lang::txt("gegenstand")." = '".Lang::txt("Teekanne")."' OR ".Lang::txt("gegenstand")." = '".Lang::txt("Ring")."')");
		$e[14]->setVerificationCount(2);

		$e[15] = new Exercise(Lang::txt("Sehr gut! Helga gibt dir einen Haufen Gold!"));
		$e[15]->setSolution("UPDATE ".Lang::txt("bewohner")." SET ".Lang::txt("gold")." = ".Lang::txt("gold")." + 120 WHERE ".Lang::txt("bewohnernr")." = 20");
		$e[15]->setSolved(true);

		$e[16] = new Exercise(Lang::txt("Leider reicht das noch nicht für ein Schwert. Dann musst du wohl doch arbeiten. Bevor du dich jedoch irgendwo bewirbst, solltest du vielleicht deinen Namen von Fremder auf deinen richtigen Namen ändern, ansonsten wird dich niemand einstellen."));
		$e[16]->setSolution("UPDATE ".Lang::txt("bewohner")." SET ".Lang::txt("name")." = 'Peter' WHERE ".Lang::txt("bewohnernr")." = 20");
		$e[16]->setVerificationQuery("SELECT * FROM ".Lang::txt("bewohner")." WHERE ".Lang::txt("name")." = '".Lang::txt("Fremder")."'");
		$e[16]->setVerificationCount(0);

		$e[17] = new Exercise(Lang::txt("In deiner Freizeit backst du gerne. Wie wäre es, wenn du dir ein bisschen Geld als Bäcker verdienst? Liste zunächst alle Bäcker auf. Mit ORDER BY gold kannst du die Liste sortieren, mit ORDER BY gold DESC steht sogar der reichste Bäcker oben."));
		$e[17]->setSolution("SELECT * FROM ".Lang::txt("bewohner")." WHERE ".Lang::txt("beruf")." = '".Lang::txt("Baecker")."' ORDER BY ".Lang::txt("gold")." DESC");

		$e[18] = new Exercise(Lang::txt("%%%PLAYER_NAME%%%: \"Hey Paul, kann ich bei dir als Bäcker arbeiten?\"\nPaul: \"Öh, na klar, %%%PLAYER_NAME%%%! Ich zahle dir pro hundert Brötchen, die du mir backst 1 Gold.\"\n8 Stunden später...\n%%%PLAYER_NAME%%%: \"Hier bittesehr, zehntausend Brötchen!\"\nPaul: \"Okay..., hier hast du 100 Gold, als nächstes...\"\n%%%PLAYER_NAME%%%: \"Ich kündige! Ich habe nun genug Gold, um mir ein Schwert zu kaufen!\".\nSchauen wir, was mit deinem Kontostand passiert:")); 
		$e[18]->setSolution("UPDATE ".Lang::txt("bewohner")." SET ".Lang::txt("gold")." = ".Lang::txt("gold")." + 100 - 150 WHERE ".Lang::txt("bewohnernr")." = 20");
		$e[18]->setSolved(true);

		$e[19] = new Exercise(Lang::txt("Ernst Peng: \"Hier ist dein neues Schwert! Nun kannst du überall hin!\""));
		$e[19]->setSolution("INSERT INTO ".Lang::txt("gegenstand")." (".Lang::txt("gegenstand").", ".Lang::txt("besitzer").") VALUES ('".Lang::txt("Schwert")."', 20)");
		$e[19]->setSolved(true);

		$e[20] = new Exercise(Lang::txt("Gibt es auf der Insel einen Piloten? Er könnte dich nach Hause fliegen?"));
		$e[20]->setSolution("SELECT * FROM ".Lang::txt("bewohner")." WHERE ".Lang::txt("beruf")." = '".Lang::txt("Pilot")."'");
		
		$e[21] = new Exercise(Lang::txt("Oh, er hat den Status 'gefangen'. Du erfährst von anderen Bewohnern, dass Dirty Dieter ihn gefangen hält. Ich kenne einen Trick, wie wir schnell herausfinden können, in welchem Dorf Dirty Dieter wohnt:"));
		$e[21]->setSolution("SELECT ".Lang::txt("dorf").".".Lang::txt("name")." FROM ".Lang::txt("dorf").", ".Lang::txt("bewohner")." WHERE ".Lang::txt("dorf").".".Lang::txt("dorfnr")." = ".Lang::txt("bewohner").".".Lang::txt("dorfnr")." AND ".Lang::txt("bewohner").".".Lang::txt("name")." = '".Lang::txt("Dirty Dieter")."'");
		$e[21]->setSolved(true);

		$e[22] = new Exercise(Lang::txt("Auf diese Weise suchen wir das Dorf mit der Dorf-Nummer, die bei Dirty Dieter im Feld dorfnr steht. Ein solch genialer Ausdruck nennt sich Verbund oder Join. Wir müssen nun den Häuptling des Dorfes Zwiebelhausen herausfinden. Hinweis: Im Feld haeuptling der Tabelle dorf steht eine bewohnernr."));
		$e[22]->setSolution("SELECT ".Lang::txt("bewohner").".".Lang::txt("name")." FROM ".Lang::txt("dorf").", ".Lang::txt("bewohner")." WHERE ".Lang::txt("dorf").".".Lang::txt("haeuptling")." = ".Lang::txt("bewohner").".".Lang::txt("bewohnernr")." AND ".Lang::txt("dorf").".".Lang::txt("name")." = '".Lang::txt("Zwiebelhausen")."'");
		
		$e[23] = new Exercise(Lang::txt("Sehr gut! Jetzt gehen wir Fritz besuchen, um ihn nach Dirty Dieter und dem Piloten zu fragen. Unterwegs überlegen wir uns, wie viele Einwohner Zwiebelhausen eigentlich hat:"));
		$e[23]->setSolution("SELECT COUNT(*) FROM ".Lang::txt("bewohner").", ".Lang::txt("dorf")." WHERE ".Lang::txt("dorf").".".Lang::txt("dorfnr")." = ".Lang::txt("bewohner").".".Lang::txt("dorfnr")." AND ".Lang::txt("dorf").".".Lang::txt("name")." = '".Lang::txt("Zwiebelhausen")."'");
		$e[23]->setSolved(true);

		$e[24] = new Exercise(Lang::txt("Fritz sagt uns, dass Dirty Dieter den Piloten im Haus seiner Schwester gefangen hält. Wie viele Frauen gibt es in Zwiebelhausen? (Hinweis: Frauen erkennt man an geschlecht = 'w')"));
		$e[24]->setSolution("SELECT COUNT(*) FROM ".Lang::txt("bewohner").", ".Lang::txt("dorf")." WHERE ".Lang::txt("dorf").".".Lang::txt("dorfnr")." = ".Lang::txt("bewohner").".".Lang::txt("dorfnr")." AND ".Lang::txt("dorf").".".Lang::txt("name")." = '".Lang::txt("Zwiebelhausen")."' AND ".Lang::txt("bewohner").".".Lang::txt("geschlecht")." = '".Lang::txt("w")."'");

		$e[25] = new Exercise(Lang::txt("Oh, nur eine Frau. Wie heißt sie?"));
		$e[25]->setSolution("SELECT ".Lang::txt("bewohner").".".Lang::txt("name")." FROM ".Lang::txt("bewohner").", ".Lang::txt("dorf")." WHERE ".Lang::txt("dorf").".".Lang::txt("dorfnr")." = ".Lang::txt("bewohner").".".Lang::txt("dorfnr")." AND ".Lang::txt("dorf").".".Lang::txt("name")." = '".Lang::txt("Zwiebelhausen")."' AND ".Lang::txt("bewohner").".".Lang::txt("geschlecht")." = '".Lang::txt("w")."'");

		$e[26] = new Exercise(Lang::txt("Im Haus von Dirty Dörthe findest du Dirty Dieter, Dirty Dörthe und den Piloten.\nDirty Dieter: \"%%%PLAYER_NAME%%%, gib mir alles Gold, was die Bewohner von unserem Nachbardorf Gurkendorf zusammen besitzen und ich lasse den Piloten frei\".\nMal schauen wie viel Gold das ist:"));
		$e[26]->setSolution("SELECT SUM(".Lang::txt("bewohner").".".Lang::txt("gold").") FROM ".Lang::txt("bewohner").", ".Lang::txt("dorf")." WHERE ".Lang::txt("dorf").".".Lang::txt("dorfnr")." = ".Lang::txt("bewohner").".".Lang::txt("dorfnr")." AND ".Lang::txt("dorf").".".Lang::txt("name")." = '".Lang::txt("Gurkendorf")."'");
		$e[26]->setSolved(true);

		$e[27] = new Exercise(Lang::txt("So viel Gold wirst du niemals allein durch Brötchenbacken verdienen können. Wir müssen uns was anderes einfallen lassen. Wenn wir Gegenstände sammeln und verkaufen und zusätzlich noch als Bäcker arbeiten, können wir maximal so viel Gold bekommen, wie die Händler, Kaufmänner und Bäcker zusammen besitzen. Wie viel ist das?"));
		$e[27]->setSolution("SELECT SUM(".Lang::txt("bewohner").".".Lang::txt("gold").") FROM ".Lang::txt("bewohner")." WHERE ".Lang::txt("beruf")." = '".Lang::txt("Haendler")."' OR ".Lang::txt("beruf")." = '".Lang::txt("Kaufmann")."' OR ".Lang::txt("beruf")." = '".Lang::txt("Baecker")."'");
		
		$e[28] = new Exercise(Lang::txt("Das ist viel zu wenig. Schauen wir mal die gesamten sowie durchschnittlichen Goldvorräte der einzelnen Berufe an:"));
		$e[28]->setSolution("SELECT ".Lang::txt("beruf").", SUM(".Lang::txt("bewohner").".".Lang::txt("gold")."), AVG(".Lang::txt("bewohner").".".Lang::txt("gold").") FROM ".Lang::txt("bewohner")." GROUP BY ".Lang::txt("beruf")." ORDER BY AVG(".Lang::txt("bewohner").".".Lang::txt("gold").")");
		$e[28]->setSolved(true);

		$e[29] = new Exercise(Lang::txt("Interessant, die Metzger haben also das meiste Gold. Warum auch immer... Wie viel Gold haben im Durchschnitt die einzelnen Bewohnergruppen je nach Status (friedlich, böse, gefangen)?"));
		$e[29]->setSolution("SELECT ".Lang::txt("status").", AVG(".Lang::txt("bewohner").".".Lang::txt("gold").") FROM ".Lang::txt("bewohner")." GROUP BY ".Lang::txt("status")."");

		$e[30] = new Exercise(Lang::txt("Okay, wir müssten also die bösen Bewohner ausrauben. Dann können wir auch gleich Dirty Dieter mit dem Schwert töten und den Piloten befreien:"));
		$e[30]->setSolution("DELETE FROM ".Lang::txt("bewohner")." WHERE ".Lang::txt("name")." = '".Lang::txt("Dirty Dieter")."'");
		$e[30]->setSolved(true);

		$e[31] = new Exercise(Lang::txt("Oh oh, jetzt ist Dirty Dörthe sauer. Was tun wir da?"));
		$e[31]->setSolution("DELETE FROM ".Lang::txt("bewohner")." WHERE ".Lang::txt("name")." = 'Dirty Dörthe'");
		$e[31]->setVerificationQuery("SELECT * FROM ".Lang::txt("bewohner")." WHERE ".Lang::txt("name")." = '".Lang::txt("Dirty Doerthe")."'");
		$e[31]->setVerificationCount(0);

		$e[32] = new Exercise(Lang::txt("Sehr gut! Befreie nun den Piloten!"));
		$e[32]->setSolution("UPDATE ".Lang::txt("bewohner")." SET ".Lang::txt("status")." = '".Lang::txt("friedlich")."' WHERE ".Lang::txt("beruf")." = '".Lang::txt("Pilot")."'");
		$e[32]->setVerificationQuery("SELECT * FROM ".Lang::txt("bewohner")." WHERE ".Lang::txt("status")." = '".Lang::txt("friedlich")."' AND ".Lang::txt("beruf")." = '".Lang::txt("Pilot")."'");
		$e[32]->setVerificationCount(1);
	
		$e[33] = new Exercise(Lang::txt("Der Pilot dankt dir und fliegt dich nach Hause. Als Andenken nimmst du dein Schwert, ein bisschen Gold und die nutzlosen Gegenstände mit nach Hause. Was für ein Abenteuer!"));
		$e[33]->setSolution("DELETE FROM ".Lang::txt("bewohner")." WHERE ".Lang::txt("bewohnernr")." = 20");
		$e[33]->setSolved(true);

		if($this->currentExercise >= count($e)) {
			return null;
		}

		return $e[$this->currentExercise];
	}
}
