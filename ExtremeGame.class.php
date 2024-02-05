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

		$e[0] = new Exercise("Nenne zu jedem Beruf den Namen und die Menge an Gold der reichsten Person, die diesen Beruf hat.
");
		$e[0]->setSolution("select beruf, name, gold from bewohner A where not exists (select * from bewohner B where B.beruf = A.beruf AND b.gold > a.gold)
");

		$e[1] = new Exercise("Die Bäcker der Insel waren auf einer Bäcker-Konferenz. Zwei der dort anwesenden Personen litten an dem Mehl-Fieber, welches hoch ansteckend ist. Zwar kann man nur Leute des gleichen Geschlechts wie man selbst anstecken, jedoch war sowohl eine Frau als auch ein Mann erkrankt. Das heißt, dass nun alle Bäcker vom Mehl-Fieber infiziert wurden und die Krankheit mit in ihr Dorf bringen und die Personen, die in diesem Dorf wohnen und das gleiche Geschlecht haben, anstecken. Wer ist alles angesteckt? Gib nur den Namen aus.
");
		$e[1]->setSolution("select name from bewohner where (dorfnr||geschlecht) in (SELECT dorfnr||geschlecht FROM bewohner where beruf = 'Baecker')
");


		$e[2] = new Exercise("Welche Bewohner (alle Spalten) haben einen Beruf, den im gleichen Dorf keine weitere Person hat?");
		$e[2]->setSolution("select * from bewohner A where not exists (select * from bewohner B where B.dorfnr=A.dorfnr AND B.beruf = A.beruf AND B.bewohnernr != A.bewohnernr)");


		$e[3] = new Exercise("In welchem Dorf gibt es eine Berufsgruppe, die es sonst in keinem Dorf gibt? Nenne mir den Namen des Dorfes sowie den Beruf, um den es sich handelt. Berufe, die es generell nur einmal auf der Insel gibt, zählen nicht dazu!");
		$e[3]->setSolution("select (select name from dorf where dorfnr=A.dorfnr) as dorf, beruf from bewohner A where not exists (select * from bewohner B where B.beruf=A.beruf AND B.dorfnr!=A.dorfnr) group by dorf, beruf having count(*)>1");


		$e[4] = new Exercise("Nenne zu jedem Gegenstand den Namen des Besitzers. Sollte der Gegenstand niemandem gehören, soll ein Strich - beim Namen stehen.
");
		$e[4]->setSolution("select gegenstand, COALESCE(name, '-') from gegenstand left outer join bewohner on gegenstand.besitzer = bewohner.bewohnernr
");




		$e[5] = new Exercise("Liste den Namen und die Anzahl der ihnen gehörenden Gegenstände ALLER Bewohner auf");
		$e[5]->setSolution("SELECT name, COUNT(gegenstand) FROM bewohner LEFT OUTER JOIN gegenstand ON besitzer=bewohnernr group by name");


		$e[6] = new Exercise("Irene Hutmacher ist auf Partnersuche. Ihr Traumpartner sollte männlich sein und möglichst viel Gold besitzen. Ihr ist ein friedlicher Bewohner lieber als ein böser. Ein Pilot kommt für sie nicht in Frage. Welche Personen kommen überhaupt noch in Frage? Hinweis: Es scheiden nicht nur Frauen und Piloten aus, sondern auch solche Männer, die von einem anderen Mann in Sachen Gold oder Status dominiert werden. Beispeilsweise kommt Ernst Peng nicht in Frage, weil Paul Backmann ebenfalls friedlich ist und sogar mehr Gold hat. Gib nur den Namen aus.
");
		$e[6]->setSolution("SELECT name FROM bewohner A where geschlecht = 'm' and beruf != 'Pilot' and not exists (select * from bewohner B where geschlecht = 'm' and beruf != 'Pilot' and (B.gold>=A.gold and B.status>=A.status) and (B.gold>A.gold or B.status>A.status))
");



		$e[7] = new Exercise("Erich Rasenkopf wurde vergiftet, seine Haut färbt sich grün! Wer tut ihm so etwas nur an? Es gibt mehrere Ideen: Entweder die Person war eifersüchtig auf sein Gold, hat also weniger als Erich. Oder der Übeltäter ist einfach nur böse. Auf jeden Fall war es niemand aus dem gleichen Dorf. In Affenstadt kennt man sich. Ein Häuptling kann es auch nicht gewesen sein. Der Zeuge Carl Ochse hat die Person gesehen und gesagt, dass er sie nicht kannte. Sie habe noch keine Gegenstände bei ihm gekauft. Spazierstöcke und Hämmer gibt es nur bei Carl zu kaufen. Der Pilot kann es nicht sein, er war zu dem Zeitpunkt von Dirty Dieter gefangen gehalten. Carl sagt, der Übeltäter trug ein Fußballtrikot mit seinem Nachnamen auf dem Rücken. Allerdings konnte er nur die Buchstaben S und A erkennen. Gib den Namen der Person aus, die Erich Rasenkopf vergiftet hat!
");
		$e[7]->setSolution("SELECT name FROM bewohner where (status = 'boese' or gold < (select gold from bewohner where name = 'Erich Rasenkopf')) and dorfnr != (select dorfnr from bewohner where name = 'Erich Rasenkopf') and bewohnernr not in (select haeuptling from dorf union select besitzer from gegenstand where gegenstand in ('Hammer','Spazierstock')) and beruf != 'Pilot' and name LIKE '% %s%' and name LIKE '% %a%'
");


		$e[8] = new Exercise("Es wird überlegt, eine neue Reichensteuer auf SQL Island einzuführen. Laut dieser muss einmalig der reichste Bewohner jedes Dorfes die Hälfte seines Goldes gleichmäßig auf alle Bewohner dieses Dorfes (inklusive ihm selbst) aufteilen und diesen geben. Zeige die Tabelle Bewohner mit allen ihren Spalten an plus einer Spalte gold_neu, die anzeigt, wie viel Gold die Bewohner nach der Einführung der Reichensteuer hätten.
");
		$e[8]->setSolution("select * from (select D.*, D.gold + E.steuer as gold_neu from bewohner D join (
select dorfnr, 0.5*gold / (select count(*) from bewohner B where B.dorfnr=A.dorfnr) as steuer from bewohner A where not exists (select * from bewohner C where C.dorfnr=A.dorfnr AND C.gold>A.gold)
) E on D.dorfnr = E.dorfnr where exists (select * from bewohner F where F.dorfnr=D.dorfnr AND F.gold>D.gold)
union
select D.*, 0.5 * D.gold + E.steuer as gold_neu from bewohner D join (
select dorfnr, 0.5*gold / (select count(*) from bewohner B where B.dorfnr=A.dorfnr) as steuer from bewohner A where not exists (select * from bewohner C where C.dorfnr=A.dorfnr AND C.gold>A.gold)
) E on D.dorfnr = E.dorfnr where not exists (select * from bewohner F where F.dorfnr=D.dorfnr AND F.gold>D.gold))");


		$e[9] = new Exercise("Wow! Glückwunsch! Du hast den Extrem-Modus von SQL Island geschafft. Das Lösungswort ist: Skyline-Anfrage");
		$e[9]->setSolution("select 'Skyline-Anfrage' as Loesungswort");
		$e[9]->SetSolved(true);


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
