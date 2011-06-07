<?php
// +----------------------------------------------------------------------+
// | Copyright (c) 2011 Marco von Gunten <marcovg@gmx.ch> 		  		  |
// +----------------------------------------------------------------------+
// | Auf dieser können die bestehenden Folien angesehen werden.	 		  |
// | Mit dem klick auf "hinzufügen" wird man auf die entsprechende Seite  |
// | weitergeleitet, beim klicken auf den bearbeiten Button wird die 	  | 
// | entsprechende Folie im bearbeitungs Modus aufgerufen.				  |
// +----------------------------------------------------------------------+

include('includes/dbConnect.inc.php');
include('includes/header.inc.php');
$message = $_GET['message'];
$anzeige = $_GET['anzeige'];
$folieBesetzt = $_SESSION['folieBesetzt'];
unset($_SESSION['folieBesetzt']);

if($folieBesetzt!=""){
	$sql = "SELECT * FROM folie WHERE `intFolie_ID` = '$folieBesetzt'";
	$result = $db->query($sql);

	if($result->num_rows){
		$row = $result->fetch_assoc();
		
		$startZeit=formatiereZeitSQL2DE($row['dateTimeVon']);
		$endZeit = formatiereZeitSQL2DE($row['dateTimeBis']);		
	}
}

?>

<h1>FOLIEN VERWALTUNG</h1>
<?php 
switch($message){
	case 1:
		echo'<div id="green">Die Folie wurde erfolgreich gespeichert!</div>';
		if($_SESSION['bild_resize']){
			echo'<div id="red">Bitte beachten Sie, dass die Grösse des Bildes auf 1280x768px angepasst wurde. Überprüfen Sie bitte, ob das Bild nicht verzerrt wurde.</div>';
		unset($_SESSION['bild_resize']);
		}
		break;
	case 2: 
		echo'<div id="red">Die Folie konnte nicht gespeichert werden! Bitte Endzeit nach der Startzeit setzen!</div>';
		break;
	case 3:
		echo'<div id="green">Der Eintrag wurde erfolgreich gelöscht!</div>';
		break;
	case 4:
		echo'<div id="green">Der Eintrag wurde erfolgreich überschrieben!</div>';
		break;
	case 5:
		echo'<div id="red">Die Folie konnte nicht gespeichert werden! Bitte überprüfen Sie Ihre Datei! <br>
		Beachten Sie, dass Ihre Datei die maximale Grösse von 1MB nicht überschreiten darf, und eine jpeg, gif oder png Datei sein muss!</div>';
		break;
	case 8:
		echo'<div id="red">Die Folie konnte nicht gespeichert werden! Es existiert bereits ein Eintrag vom '.$startZeit.' bis zum '.$endZeit.'!</div>';
		break;
	case 9:
		echo'<div id="red">Die Folie konnte nicht gespeichert werden! Der ausgewählte Termin liegt in der Vergangenheit!</div>';
		break;
	case 10:
		echo'<div id="green">Die Galerie wurde erfolgreich gespeichert!</div>';
		break;
	case 11:
		echo'<div id="red">Die Galerie konnte nicht gespeichert werden! Fehler beim entpacken des Zip-Archivs!</div>';
		break;
	case 12:
		echo'<div id="red">Die Galerie konnte nicht gespeichert werden! Es dürfen nur .zip Dateien verwendet werden!</div>';
		break;
	case 13:
		echo'<div id="red">Die Galerie konnte nicht gespeichert werden! Das Zip-Archiv ist invalid!</div>';
		break;
}
?>
<h2>NEUE FOLIE</h2>
<a href="?pid=6&&track=1">Template-Folie hinzufügen</a><br>
<a href="?pid=6&&track=2">Bild-Folie hinzufügen</a><br>
<a href="?pid=6&&track=5">Galerie hinzufügen</a>

<h2>VERWALTEN</h2>
<?php 
if($anzeige==""){
echo'<a href="?pid=0" style="color: black;">Zukünftige</a> |'; 
}
else{
	echo'<a href="?pid=0">Zukünftige</a> |';
}

if($anzeige=="2"){
echo'<a href="?pid=0&&anzeige=2" style="color: black;">Vergangene</a> |'; 
}
else{
	echo'<a href="?pid=0&&anzeige=2">Vergangene</a> |';
}
if($anzeige=="1"){
echo'<a href="?pid=0&&anzeige=1" style="color: black;">Alle</a><br><br>'; 
}
else{
	echo'<a href="?pid=0&&anzeige=1">Alle</a><br><br>';
}
//"Jetzt" in Variable speichern
$timestamp = time();
$datum = date("Y-m-d",$timestamp);
$uhrzeit = date("H:i",$timestamp);
$now = $datum." ".$uhrzeit;

//Alle Einträge abfragen aus der DB
if($anzeige == 1){
$sql = "SELECT f1 . * , a1.strAccountName, t1.* FROM folie f1 LEFT JOIN admin a1 ON ( f1.intAdmin_ID = a1.intAdmin_ID ) LEFT JOIN template t1 ON (f1.intTemplate_ID = t1.intTemplate_ID) ORDER BY dateTimeVon";
}

//Alle Vergangenen Einträge aus der DB abfragen 
else if($anzeige == 2){
$sql = "SELECT f1 . * , a1.strAccountName, t1.* FROM folie f1 LEFT JOIN admin a1 ON ( f1.intAdmin_ID = a1.intAdmin_ID ) LEFT JOIN template t1 ON (f1.intTemplate_ID = t1.intTemplate_ID) WHERE dateTimeBis < '$now' ORDER BY dateTimeVon DESC";	
}

//Alle Einträge welche in der Zukunf liegen abfragen aus der DB
else{
$sql = "SELECT f1 . * , a1.strAccountName, t1.* FROM folie f1 LEFT JOIN admin a1 ON ( f1.intAdmin_ID = a1.intAdmin_ID ) LEFT JOIN template t1 ON (f1.intTemplate_ID = t1.intTemplate_ID) WHERE dateTimeBis >= '$now' ORDER BY dateTimeVon";
}

$result = $db->query($sql);
//Resultate von der DB in Variablen speichern
if($result->num_rows){
	$i = 0;
	while($row = $result->fetch_assoc()){
		$id[$i] = $row['intFolie_ID'];
		$von[$i] = formatiereZeitSQL2DE($row['dateTimeVon']);
		$bis[$i] = formatiereZeitSQL2DE($row['dateTimeBis']);
		$text1[$i] = $row['strText1'];
		$text2[$i] = $row['strText2'];
		$path[$i] = $row['strPath'];
		$templatePath[$i] = $row['strPfad'];
		$templateName[$i] = $row['strName'];
		$templateID[$i] = $row['intTemplate_ID'];
		$admin[$i] = $row['strAccountName'];

		$i++;
	}

	// Resultate auflisten
	$i=0;
	while($i<sizeof($id)){
		if($id[$i]==$folieBesetzt){
			echo'<table style="border-color:red; border-style:solid;">';
		}
		else{
			echo'<table>';
		}
		//Wenn es eine Bild Folie ist
		if($templatePath[$i] == NULL){
			echo'<tr valign="top"><td rowspan=5><img src="'.$path[$i].'" width="300px"></img></td><th width="100px">Bild Folie</th></tr>
		<tr valign="top"><td>Pfad</td><td>'.$path[$i].'</td></tr>';
		}
		//Wenn es eine Template Folie ist
		else if($templateID[$i] == 1 || $templateID[$i] == 3){
			echo'
			<tr valign="top"><td rowspan=8><img src="'.$templatePath[$i].'" width="300px"></img></td><th width="100px">Template Folie</th></tr>
			<tr valign="top"><td>Template: </td><td>'.$templateName[$i].'</td></tr>
			<tr valign="top"><td>Überschrift: </td><td>'.$text1[$i].'</td></tr>		
			<tr valign="top"><td>Kurztext: </td><td>'.$text2[$i].'</td></tr>';
		}
		//Wenn es eine Galerie ist
		else if($templateID[$i] == 2){
			$folder = $path[$i];
			if($handle = opendir($folder)){
				$file = readdir($handle);
				if ($file != "." && $file != "..") {
					$bild_location = $folder."/".$file;
						
				}
			}
			echo'
			<tr valign="top"><td rowspan=8><img src="'.$bild_location.'" width="300px"></img></td><th width="10px">Galerie</th></tr>
			<tr valign="top"><td>Template: </td><td>'.$templateName[$i].'</td></tr>
			<tr valign="top"><td>Name: </td><td>'.$text1[$i].'</td></tr>';	
		}
		echo'
		<tr valign="top"><td>Start: </td><td>'.$von[$i].'</td></tr>
		<tr valign="top"><td>Ende: </td><td>'.$bis[$i].'</td></tr>
		<tr valign="top"><td>Ersteller: </td><td>'.$admin[$i].'</td></tr>	
		';
		//Falls es eine BildFolie ist
		if($templatePath[$i] == NULL){
			echo'<tr><td></td><td><a href="?pid=5&&track=4&&id='.$id[$i].'&&path='.$path[$i].'" onClick="return confirm(\'Sind Sie sicher, dass Sie diesen Eintrag löschen wollen?\')"><img src="css/images/trash.png"></img></a>&nbsp;&nbsp;&nbsp;<a href="?pid=6&&track=4&&id='.$id[$i].'"><img src="css/images/edit.png"></img></a></td></tr>';
		}
		//Falls es eine Template Folie ist
		else if($templateID[$i] == 1 || $templateID[$i] == 3){
			echo'<tr><td></td><td><a href="?pid=5&&track=4&&id='.$id[$i].'&&path='.$path[$i].'" onClick="return confirm(\'Sind Sie sicher, dass Sie diesen Eintrag löschen wollen?\')"><img src="css/images/trash.png"></img></a>&nbsp;&nbsp;&nbsp;<a href="?pid=6&&track=3&&id='.$id[$i].'"><img src="css/images/edit.png"></img></a></td></tr>';
		}
		else if($templateID[$i] == 2){
			echo'<tr><td></td><td><a href="?pid=5&&track=9&&id='.$id[$i].'&&path='.$path[$i].'" onClick="return confirm(\'Sind Sie sicher, dass Sie diesen Eintrag löschen wollen?\')"><img src="css/images/trash.png"></img></a>&nbsp;&nbsp;&nbsp;<a href="?pid=6&&track=6&&id='.$id[$i].'"><img src="css/images/edit.png"></img></a></td></tr>';
		}
		echo'
		</table>
		<hr><br>';
		$i++;
	}
}

else{
	echo'Es sind keine Einträge vorhanden!';
}

//Formatiere Zeit aus SQL ins deutsche Format
function formatiereZeitSQL2DE ($zeit){
	//Trennt Datum und Zeit voneinander
	$teile = explode(" ", $zeit);
	$date = $teile[0];
	$time = $teile[1];

	//Teilt das Datum bei den "-" und fügt es im dateNew in der gewünschten Reihenfolge und von "." statt "-" separiert wieder zusammen
	$teile = explode("-", $date);
	$dateNew = $teile[2].".".$teile[1].".".$teile[0];

	//Die Zeit wird bei den ":" getrennt, anschliessend ohne den letzten Teil wieder zusammengeführt
	$teile = explode(":", $time);
	$zeitNew = $teile[0].":".$teile[1];

	//Die neue Zeit wird aus dem neuen Datum und der neuen Zeit zusammengesetzt
	$zeit = $dateNew." ".$zeitNew;

	return $zeit;
}
include('includes/footer.inc.php');
?>