<!DOCTYPE html>
<html>
<head>

<!--+-----------------------------------------------------------------------+-->
<!--| Diese Datei ist für die Anzeige verantwortlich. Es wird hier nicht der|-->
<!--| Standard-Header verwendet,sondern eine abgespeckte integrierte Version|-->
<!--| Er beinhaltet einen meta-refresh, um die Datei selbst immer wieder neu|-->
<!--| zu laden. Damit rufen wir immer wieder die Überprüfung auf der DB auf.|-->
<!--+-----------------------------------------------------------------------+-->

<meta charset="utf-8" type="text/html" />
<meta http-equiv="cache-control" content="no-cache" />
<meta http-equiv="refresh" content="60"; URL=?pid=0">
<title>Anzeige Willkommensbildschirm</title>
<link rel="stylesheet" type="text/css" href="css/styleAnzeige.css" />
<link href="css/styleSlideshow.css" rel="stylesheet"  type="text/css" media="screen"/>
<link rel="icon" href="css/images/favicon.png" type="image/x-icon">
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
<script src="js/galleria/galleria-1.2.3.min.js" type="text/javascript"></script>
<script src="js/functions.js" type="text/javascript"></script>
</head>
<?php
include('includes/dbConnect.inc.php');

//"Jetzt" in Variable speichern
$timestamp = time();
$datum = date("Y-m-d",$timestamp);
$uhrzeit = date("H:i",$timestamp);
$now = $datum." ".$uhrzeit;

//Überprüfen ob Eintrag vorhanden ist der zur aktuellen Zeit dargestellt werden sollte
$sql = "SELECT f1.* , t1.strPfad FROM folie f1 LEFT JOIN template t1 ON (f1.intTemplate_ID = t1.intTemplate_ID) WHERE '$now' BETWEEN `dateTimeVon` AND `dateTimeBis`";

$result = $db->query($sql);

if (!$result) {
	die('Der Query konnte nicht ausgeführt werden: '.$db->error);
}

//Falls Resultate zurückgegeben werden, überprüfen, was für ein Folien-Typ
if($result->num_rows){
	$row = $result->fetch_assoc();
	$templateID = $row['intTemplate_ID'];
	
	//Falls Bild-Folie
	if($templateID == "0"){
			unset($_SESSION['showCounter']);	
			$path = $row['strPath'];
			echo"<body style=\"background: url('".$path."') no-repeat; background-color: black;\">
			</body>";
	}
	//Falls Template Folie
	else if($templateID =="1" || $templateID =="3"){
	unset($_SESSION['showCounter']);	
		
	$ueberschrift = $row['strText1'];
	$text = $row['strText2'];
	$empfang = $row['strEmpfang'];
	$path = $row['strPfad'];
	
	echo"<body style=\"background: url('".$path."') no-repeat; background-color: black; cursor: url('css/images/leer.cur'), move;\">
	<div id='mouseHidden'><div id='information'>
	<br><br><br><h1>".$ueberschrift."</h1><br>"
		.$text."</div>
	<div id='empfang'>".$empfang."</div></div>
	</body>";
	}
	
	//Falls Galerie
	else if($templateID =="2"){	
		$text = $row['strText1'];	
		$path = $row['strPath'];
		//Wenn die Counter-Session noch nicht gesetzt ist auf 0 setzen
		if(!isset($_SESSION['showCounter'])){
		$_SESSION['showCounter'] = 1;	
		}
		//Alle Bild-Pfade ins pic-Array speichern
		$x =1;
		foreach(glob($path."/*") as $bild){
		$pic[$x] = $bild;
		$x++; 	
		}
		
		echo"
<body>
<div id='mouseHidden'>
<div id='gallery'>";
		$i = 0;

		//Sechs Bilder in einer Minute anzeigen, da Refresh nach 60 Sekunden ist, und ein Bild 10 Sekunden angezeigt wird.
		while($i<6){
				echo'<img src="'.$pic[$_SESSION[showCounter]].'">';
							
			$_SESSION['showCounter']++;
			$i++;
			
			if($_SESSION['showCounter']>= $x){
				$_SESSION['showCounter'] = 1;
			}
		}
		echo'
		</div>
		</div>
	<div id="galleryFooter">
	<div id="galleryFooterText">'.$text.'</div></div>
	
			<script>
            Galleria.loadTheme("js/galleria/themes/classic/galleria.classic.min.js");
            $("#gallery").galleria({
            	width: 1250,
        		height: 750,
        		autoplay: 9500,
        		showImagenav: false,
        		showCounter: false,
        		thumbnails: false,
        		imageMargin: 20,
        		transition: "flash"
            });
        </script>
        
		</body>';	
}
}
else{
	unset($_SESSION['showCounter']);	
	echo"<body style=\"background: url('/css/images/templates/standardTemplate.png') no-repeat; background-color: black;\">
</body><div id='mouseHidden'></div>";
}
?>
</html>