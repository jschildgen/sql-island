<?php
session_start();
header("Content-Type:text/html; charset=utf-8");

if(isset($_GET['lang'])) {
	session_destroy();
	session_start();
	$_SESSION['lang'] = $_GET['lang'];
	header("Location:.");
}

if(isset($_REQUEST['load_game_id']) 
   && preg_match('/^[A-Za-z0-9]{10}$/', trim($_REQUEST['load_game_id'])) == 1) {
	/*session_destroy();
	session_start();
	$_SESSION['lang'] = $_GET['lang'];
	header("Location:.");*/
  $game_id = trim($_REQUEST['load_game_id']);
  $level = -1;
  $dbfile = null;
  foreach (glob("DBs/save/".$game_id."_*.sqlite") as $filename) {
      preg_match('#DBs/save/([^_]*)_([0-9]+)_([^_]*)_([^_]*).sqlite#', $filename, $matches);
      if( ((int)$matches[2]) > $level) {  // if multiple files: the one with max level
        $level = (int)$matches[2];
        if($matches[3] != "") { $_SESSION['lang'] = $matches[3]; }
        if($matches[4] == "extreme") { $_SESSION['extreme'] = true; }
        $dbfile = $filename;
      }
      if($dbfile != null) {
        $_SESSION['dbID'] = $game_id;
        $_SESSION['currentExercise'] = $level;
        copy($dbfile, "DBs/".$game_id.".sqlite");
      }
  }
  
}

require_once("./DB.class.php");
require_once("Lang.class.php");

if(@$_GET['mode'] == "extreme") {
	session_destroy();
	session_start();
	$_SESSION['extreme'] = true;
	header("Location:.");
}

if(@$_GET['mode'] == "sandbox") {
  session_destroy();
  session_start();
	$_SESSION['sandbox'] = true;
  header("Location:.");
}

if(isset($_SESSION['dbID'])) {
	$db = new DB($_SESSION['dbID']);
} else {
	$db = new DB();
	$_SESSION['dbID'] = $db->getDbID();
}



if(@$_SESSION['extreme'] === true) { 
  require_once("./ExtremeGame.class.php"); 
} elseif(@$_SESSION['sandbox'] === true) {
  require_once("./SandboxGame.class.php");
} else { 
  require_once("./Game.class.php"); 
}

if(isset($_SESSION['currentExercise'])) {
        $game = new Game($_SESSION['currentExercise']);
} else {
        $game = new Game();
}

?>

<!doctype html>
<html class="no-js" lang="<?=Lang::getLanguage();?>">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>SQL Island</title>
    <link rel="stylesheet" href="css/foundation.min.css" />
    <link rel="stylesheet" href="css/app.min.css" />
    <link rel="stylesheet" href="css/animation.min.css" />
    <script type="text/javascript"  src="./js/jquery-1.11.3.min.js"></script>
    <script type="text/javascript"  src="./js/ace/ace.js"></script>
    <script type="text/javascript"  src="./js/sqlisland.js"></script>
    <link rel="stylesheet" href="./css/highlight/agate.css">
    <link rel="stylesheet" href="./css/ladda.min.css">
    <link rel="icon" href="./favicon.ico" type="image/x-icon">    
    <script src="./js/highlight.pack.js"></script>
    <script>hljs.initHighlightingOnLoad();</script>

  </head>
  <body>
    <div class="ribbon clearfix hide-on-phones">
        <div class="left">&nbsp;</div>
        <a href="" class="menu-button red button" id="menu-button"></a>
        <div class="title"><h1>SQL Island</h1></div>
        <div class="right">&nbsp;</div>
        <div class="logo"><img src="./images/wf/logo.png" width="179" height="94" alt="SQL Island" ></div>
    </div>

    <div class="row show-on-phones">
        <div class="columns twelve phone-logobar"><img src="./images/wf/logo.png" alt="SQL Island" width="120"></div>
        <div class="columns twelve phone-titlebar"><h1>SQL Island</h1></div>
        <a href="" class="menu-button button red mobile"></a>
    </div>

    <div class="menu-content">
            <ul class="menu">
                <li><a href="#" class="medium red button radius" id="joyride-button"><?=Lang::txt('Spielanleitung');?></a></li>
                <li><a href="#" data-reveal-id="restart-modal" class="medium red button radius" id="restart-button"><?=Lang::txt('Spiel neustarten');?></a></li>
                <li><a href="#" data-reveal-id="save-load-modal" class="medium red button radius" id="save-load-button"><?=Lang::txt('Spiel speichern / laden');?></a></li>
								<li><a href="#" data-reveal-id="language-modal" class="medium red button radius" id="restart-button"><?=Lang::txt('Sprache wechseln'); if($_SESSION['lang']!=="en") { echo "<br>Change Language"; } ?></a></li>
                <li><a href="#" data-reveal-id="sandbox-modal" class="medium red button radius" id="sandbox-button"><?=Lang::txt('Sandbox-Modus');?></a></li>
								<li><a href="#" data-reveal-id="videoModal" class="medium red button radius" id="restart-button"><?=Lang::txt('Game-Trailer Video anschauen');?></a></li>
								<li><a href="#" data-reveal-id="info-modal" class="medium red button radius" id="restart-button"><?=Lang::txt('Info');?></a></li>
            </ul>

            <span class="menu-pointer-bg clearfix"></span>
            <span class="menu-pointer clearfix"></span>
    </div>
    <div class="menu-bg" style="display: block; cursor: pointer; opacity: 0.4;"></div>


    <div id="screen-index" class="container" style="margin-top:15px;">

        <div class="row text-box-container">
          <div class="four columns" id="story">
            <div class="offset-by-one animated bounceIn" id="bubble">
                <div class="text-box clearfix" style="width:250px">
                    <!--<h2>Wow!</h2>-->
                    <h3 id="exercise_text"><?php if($game->getExercise() != null && $_SESSION['sandbox'] !== true) { $game->setPlayerName($db->getPlayerName()); echo $game->getExercise()->getDescription(); } ?></h3>
                    <!--<p>Zeige mir die Liste der Bewohner.</p>-->
		    <a id="continue_button" class="large red button radius right"><?=Lang::txt('Weiter');?></a>
		    <a id="certificate_button" class="large green button radius right" style="display:none" href="cert.php" target=_blank"><?=Lang::txt('Zertifikat');?></a>
                    <span class="text-box-pointer clearfix"></span>
                    <span class="text-box-pointer-shadow"></span>
                </div>
            </div>

            <div class="row bg-image-container">
              <div id="leftimg" class="one column bg-avatar animated fadeInLeft"></div>
							<div id="rightimg" class="offset-by-four hide-on-phones bg-avatar2"></div>
            </div>
          </div>
            <div class="eight columns" id="query_section">
                <div class="text-box clearfix" id="querylog"><pre><code id="testo"></code></pre></div>

                <!--<div class="text-box clearfix" style="margin-top:25px;">-->
								<div class="panel" id="editor" style="margin-top:25px;"></div>
                <!--</div>-->

								<div class="row">
                <button title="<?=Lang::txt('Strg+Enter');?>" id="submit-query-button" class="large red button radius right" data-style="expand-right" style="margin-top:-5px;"><span class="ladda-label"><?=Lang::txt('Ausführen');?></span></button>
								</div>
            </div>
        </div>



    </div>


    <!--<div class="row">
        <div class="twelve columns bottom-border"></div>
    </div>-->

    <div class="row" id="tablelist">
        <div class="columns twelve button save-button radius">
					<div class="columns"><?php echo Lang::txt('DORF').' ('.Lang::txt('dorfnr').', '.Lang::txt('name').', '.Lang::txt('haeuptling').')'; ?></div>
					<div class="columns"><?php echo Lang::txt('BEWOHNER').' ('.Lang::txt('bewohnernr').', '.Lang::txt('name').', '.Lang::txt('dorfnr').', '.Lang::txt('geschlecht').', '.Lang::txt('beruf').', '.Lang::txt('gold').', '.Lang::txt('status').')'; ?></div>
					<div class="columns"><?php echo Lang::txt('GEGENSTAND').' ('.Lang::txt('gegenstand').', '.Lang::txt('besitzer').')'; ?></div>
				</div>
    </div>


<div id="restart-modal" class="reveal-modal tiny" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
  <h2 id="modalTitle"><?=Lang::txt('Spiel neustarten');?></h2>
  <p class="lead"><?=Lang::txt('Bist du dir sicher?');?></p>
  <p><?=Lang::txt('Wenn du das Spiel neustartest, fängst du wieder ganz von vorne an.');?></p>
  <p><a href="#" id="really-restart-button" class="button"><?=Lang::txt('Ja, Neustart!');?></a>
  <a href="#" id="not-restart-button" class="button"><?=Lang::txt('Nein, weiterspielen!');?></a></p>
  <a class="close-reveal-modal" aria-label="Close">&#215;</a>
</div>

<div id="sandbox-modal" class="reveal-modal tiny" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
  <h2 id="modalTitle"><?=Lang::txt('Sandbox-Modus');?></h2>
  <p class="lead"><?=Lang::txt('Bist du dir sicher?');?></p>
  <p><?=Lang::txt('Wenn du den Sandbox-Modus startest, wird das Spiel beendet und du kannst frei auf der Datenbank arbeiten.');?></p>
  <p><a href="#" id="really-sandbox-button" class="button"><?=Lang::txt('Ja!');?></a>
  <a href="#" id="not-sandbox-button" class="button"><?=Lang::txt('Nein, weiterspielen!');?></a></p>
  <a class="close-reveal-modal" aria-label="Close">&#215;</a>
</div>

<div id="save-load-modal" class="reveal-modal tiny" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
  <h2 id="modalTitle"><?=Lang::txt('Spiel speichern / laden');?></h2>
  <p><?=Lang::txt('Wenn du das Spiel speicherst, kannst du beim nächsten Mal hier weitermachen.');?></p>
  <p><a href="#" id="save-button" class="button"><?=Lang::txt('Speichern');?></a></p>
  <form method="POST" action="."><p><?=Lang::txt('Um ein Spiel zu laden, gib hier die Spiel-ID ein:');?> <input size="10" name="load_game_id"> <input type="submit" id="load-button" class="button" value="<?=Lang::txt('Laden');?>"></input></form></p>
  <a class="close-reveal-modal" aria-label="Close">&#215;</a>
</div>

<div id="save-modal" class="reveal-modal tiny" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
  <h2 id="modalTitle"><?=Lang::txt('Gespeichert');?></h2>
  <p class="lead"><?=Lang::txt('Notiere dir deine Spiel-ID: '); ?><span style="font-weight:bold;" id="game_id"></span></p>
  <a class="close-reveal-modal" aria-label="Close">&#215;</a>
</div>

<div id="language-modal" class="reveal-modal tiny" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
  <h2 id="modalTitle"><?=Lang::txt('Sprache wechseln');?></h2>
  <p class="lead"><?=Lang::txt('Bitte wähle eine Sprache.');?></p>
  <p><?=Lang::txt('Danach wird das Spiel neu gestartet');?></p>
  <p><a href="./?lang=de" class="button">Deutsch</a>
     <a href="./?lang=en" class="button">English</a>
     <a href="./?lang=pt" class="button">Português</a>
     <a href="./?lang=fr" class="button">Français</a>
     <a href="./?lang=hu" class="button">Magyar / Hungarian</a>
  <a class="close-reveal-modal" aria-label="Close">&#215;</a>
</div>

<div id="videoModal" class="reveal-modal large" data-reveal aria-labelledby="videoModalTitle" aria-hidden="true" role="dialog">
  <h2 id="videoModalTitle"><?=Lang::txt('SQL Island Game Trailer');?></h2>
  <div class="flex-video widescreen vimeo">
    <iframe width="1280" height="720" src="https://www.youtube.com/embed/aMmHYE2N5MM" frameborder="0" allowfullscreen></iframe>
  </div>
</div>

<div id="info-modal" class="reveal-modal tiny" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
  <h2 id="modalTitle">http://www.sql-island.de - SQL Island</h2>
  <p class="lead"><?=Lang::txt('Ein Text-Adventure-Lernspiel für die Datenbanksprache SQL');?></p>
  <p><?=Lang::txt('Das Spiel wurde an der Technischen Universität Kaiserslautern in der AG Heterogene Informationssysteme / Lehrgebiet Informationssysteme von Johannes Schildgen entwickelt. Ein großer Dank geht an Isabell Ruth, die die Grafiken erstellt hat, sowie an Marlene van den Ecker für die Übersetzung ins Englische, Adilson Vahldick für die portugiesische Übersetzung und Hervé L\'helguen für die Übersetzung ins Französische.');?></p>
	<p><a href="http://wwwlgis.informatik.uni-kl.de/cms/courses/informationssysteme/sqlisland/" class="button"><?=Lang::txt('Weitere Infos, Impressum, Kontakt und Publikationen'); ?></a></p>
  <a class="close-reveal-modal" aria-label="Close">&#215;</a>
</div>


<ol id="joyRideContent" class="joyride-list" data-joyride>
	<li data-id="leftimg" data-button="<?=Lang::txt('Weiter');?>" data-options="tip_location: top; prev_button: false">
		<h4><?=Lang::txt('Das bist du!');?></h4>
		<p style="font-size:12pt"><?=Lang::txt('Nach einem Flugzeugabsturz stellst du fest, dass du der einzige Überlebende bist. Du landest auf der Insel SQL Island und das Ziel des Spiels ist es, von dieser Insel zu entkommen.');?></p>
	</li>
  <li data-id="editor" data-button="<?=Lang::txt('Weiter');?>" data-prev-text="<?=Lang::txt('Zurück');?>" data-options="tip_location: top;">
    <p style="font-size:12pt"><?=Lang::txt('Hier wirst du nachher die Spielbefehle eingeben. Du steuerst das komplette Spiel mit Kommandos aus der Datenbanksprache SQL.');?></p>
	</li>
	</li>
	<li data-id="querylog" data-button="<?=Lang::txt('Weiter');?>" data-prev-text="<?=Lang::txt('Zurück');?>">
		<p style="font-size:12pt"><?=Lang::txt('Du kannst kein SQL? Keine Angst, hier werden dir im Laufe des Spiels die einzelnen Kommandos gezeigt.');?></p>
	</li>
	<li data-id="tablelist" data-button="<?=Lang::txt('Weiter');?>" data-prev-text="<?=Lang::txt('Zurück');?>" data-options="tip_location: top">
		<p style="font-size:12pt"><?=Lang::txt('Hier unten steht, welche Tabellen es gibt: Dorf, Bewohner und Gegenstand. In Klammern stehen die Spaltennamen der Tabellen. Diese Infos wirst du im Spiel ganz oft brauchen!');?></p>
	</li>
	<li data-id="menu-button" data-button="<?=Lang::txt('Los geht\'s!');?>" data-prev-text="<?=Lang::txt('Zurück');?>">
		<p style="font-size:12pt"><?=Lang::txt('Über diesen Menü-Knopf kannst du das Spiel neustarten oder dir diese Anleitung noch einmal anschauen. Aber genug geredet, lasst uns anfangen!');?></p>
	</li>
</ol>

    <script type="text/javascript">

        var editor = ace.edit("editor");
        editor.setTheme("ace/theme/eclipse");
        editor.getSession().setMode("ace/mode/sql");
        editor.renderer.setShowGutter(false);
        editor.getSession().setUseWrapMode(true);
        editor.setHighlightActiveLine(false);
        editor.setShowPrintMargin(false);
        function update() {
            var shouldShow = !editor.session.getValue().length;
            var node = editor.renderer.emptyMessageNode;
            if (!shouldShow && node) {
                editor.renderer.scroller.removeChild(editor.renderer.emptyMessageNode);
                editor.renderer.emptyMessageNode = null;
            } else if (shouldShow && !node) {
                node = editor.renderer.emptyMessageNode = document.createElement("div");
                node.textContent = "SELECT ... <--- <?=Lang::txt('Schreibe deine SQL-Anfrage hier hinein');?>";
                node.className = "ace_invisible ace_emptyMessage";
                node.style.padding = "0px 5px";
                editor.renderer.scroller.appendChild(node);
            }
        }
        editor.on("input", update);
        setTimeout(update, 100);

        editor.commands.addCommand({
            name: "submit",
            bindKey: {win: "Ctrl-Return", mac: "Command-Return"},
            exec: function(editor) {
              $('#submit-query-button').click();
            }
        });

    </script>

    <script src="./js/spin.min.js"></script>
    <script src="./js/ladda.min.js"></script>
    <script type="text/javascript" src="./js/modernizr.foundation.js"></script>
    <script type="text/javascript" src="./js/screen-common.js"></script>
    <script src="./js/foundation.min.js"></script>
    <script>
			$(document).foundation();
				$(document)
		.foundation({joyride: {
			pre_ride_callback: function() {
				$('#bubble').hide();
			},
      post_ride_callback: function() {
        $('#bubble').show();
      },
      post_step_callback: function(index, tip) {
      // Check if it's the last step
      if (index + 1 === $('#joyRideContent').find('li').length) {
        $('#bubble').show();
      }
      },
			abort_on_close : false
		}});

<?php
if($_SESSION['sandbox'] !== true) {
echo <<<JS
			jQuery(document).ready(function ($) { setTimeout(function() {
				$(document).foundation('joyride', 'start'); }, 500);
				});
JS;
} else {
echo <<<JS
   $('#story').hide();
   $('#query_section').removeClass("eight").addClass("twelve");
JS;
}
?>
    </script>
  </body>
</html>
