<?php
// +----------------------------------------------------------------------+
// | Copyright (c) 2011 Marco von Gunten <marcovg@gmx.ch> 		  		  |
// +----------------------------------------------------------------------+
// | Auf dieser Seite können die Folien bearbeitet, oder neue erstellt	  |
// | werden. Die Auswahl der Start- und End-Zeit wird mit Hilfe des		  |
// | jquery.ui.timepicker vereinfacht.									  |
// +----------------------------------------------------------------------+
include('includes/dbConnect.inc.php'); 
include('includes/header.inc.php');

$track = $_GET['track'];

switch($track){
	//Template-Folie Neu
	case 1:
?>
<h1>FOLIE ERSTELLEN</h1>
<form id="Form" name="neueFolie" action="?pid=5&&track=3" method="POST">
<table>
	<tr><td>&Uuml;berschrift: </td><td><input type="text" name="ueberschrift" value="Herzlich Willkommen" class="required" minlength="2" ></input></td></tr>
	<tr><td>Text: </td><td><textarea rows="4" cols="40" name="text" class="required" maxlength="140"></textarea></td></tr>
	<tr><td>Empfang: </td><td><input type="radio" name="empfang" value="3" checked="checked">Ascom, Login, Post &nbsp; &nbsp; &nbsp; <input type="radio" name="empfang" value="1">Ascom</td></tr>
	<tr><td>Start: </td><td><input type="text" name="startZeit" id="timepicker1" class="required" minlength="2"/></td></tr>
	<tr><td>Ende: </td><td><input type="text" name="endZeit" id="timepicker2" class="required" minlength="2"/></td></tr>
</table>
<br>
<input type="submit" value="Folie Speichern">
</form>
<?php
	break;

	//Bild-Folie Neu
	case 2: 
?>
<h1>FOLIE ERSTELLEN</h1>
<form id="Form" name="neueFolie" action="?pid=5&&track=6" onsubmit="return checkPic()" method="POST" enctype="multipart/form-data">
<table>
	<tr><td>Datei: </td><td><input type="file" name="bild"></input></td></tr>
	<tr><td>Start: </td><td><input type="text" name="startZeit" id="timepicker1" class="required" minlength="2"/></td></tr>
	<tr><td>Ende: </td><td><input type="text" name="endZeit" id="timepicker2" class="required" minlength="2"/></td></tr>
</table>
<br>
<input type="submit" value="Folie Speichern">
</form>
<?php

	break;
	
	//Template Folie bearbeiten
	case 3:

	$id = $_GET['id'];

	$sql = "SELECT * FROM folie WHERE intFolie_ID = '$id'";
	
	$result = $db->query($sql);
	
	if($result->num_rows){
		$row = $result->fetch_assoc();
		
		$id = $row['intFolie_ID'];
		$von = formatiereZeitSQL2DE($row['dateTimeVon']);
		$bis = formatiereZeitSQL2DE($row['dateTimeBis']);
		$text1 = $row['strText1'];
		$text2 = $row['strText2'];
		$text2 = str_replace("<br />", "", $text2);
		$path = $row['Path'];
		$templateID = $row['intTemplate_ID'];
	

		echo'
		<h1>FOLIE BEARBEITEN</h1>
		<form id="Form" name="folieBearbeiten" action="?pid=5&&track=5&&id='.$id.'" method="POST">
		<table>
		<tr><td>&Uuml;berschrift: </td><td><input type="text" name="ueberschrift" value="'.$text1.'" class="required" maxlength="30"></input></td></tr>
		<tr><td>Text: </td><td><textarea rows="4" cols="40" name="text" class="required" maxlength="140">'.$text2.'</textarea></td></tr>
		<tr><td>Empfang: </td><td>
		';
		if($templateID == 3){
			echo'<input type="radio" name="empfang" value="3" checked="checked">Ascom, Login, Post &nbsp; &nbsp; &nbsp;
					 <input type="radio" name="empfang" value="1">Ascom';
		}
		else{
			echo'<input type="radio" name="empfang" value="3">Ascom, Login, Post &nbsp; &nbsp; &nbsp;
					 <input type="radio" name="empfang" value="1" checked="checked">Ascom';
		}
		echo'
</td></tr>
		<tr><td>Start: </td><td><input type="text" name="startZeit" id="timepicker1" value="'.$von.'" class="required" minlength="2"/></td></tr>
		<tr><td>Ende: </td><td><input type="text" name="endZeit" id="timepicker2" value="'.$bis.'" class="required" minlength="2"/></td></tr>
	</table>
	<br>
	<input type="submit" value="Folie Speichern">
	</form>';
	}
	break;
	
	//Bild Folie bearbeiten
	case 4:

	$id = $_GET['id'];

	$sql = "SELECT * FROM folie WHERE intFolie_ID = '$id'";
	
	$result = $db->query($sql);
	
	if($result->num_rows){
		$row = $result->fetch_assoc();
		
		$id = $row['intFolie_ID'];
		$von = formatiereZeitSQL2DE($row['dateTimeVon']);
		$bis = formatiereZeitSQL2DE($row['dateTimeBis']);
		$path = $row['strPath'];

		echo'
		<h1>FOLIE BEARBEITEN</h1>
		<form id="Form" name="folieBearbeiten" action="?pid=5&&track=7&&id='.$id.'" method="POST">
		<table>
		<tr><td>Pfad:</td><td>'.$path.'</td></tr>
		<tr><td>Start: </td><td><input type="text" name="startZeit" id="timepicker1" value="'.$von.'" class="required" minlength="2"/></td></tr>
		<tr><td>Ende: </td><td><input type="text" name="endZeit" id="timepicker2" value="'.$bis.'" class="required" minlength="2"/></td></tr>
	</table>
	<br>
	<input type="submit" value="Folie Speichern">
	</form>';
	}
	break;
	
	//Galerie speichern
	case 5:
	?>
	<h1>GALERIE ERSTELLEN</h1>
	<br>Bitte wählen Sie ein beliebiges Bild im Zielordner. <br>
	Alle Bilder welche sich in diesem Ordner befinden werden anschliessend gespeichert. <br><br>
	<form id="Form" name="neueFolie" action="?pid=5&&track=8" method="POST" enctype="multipart/form-data">
	<table>
		<tr><td>Datei: </td><td><input type="file" name="zip"></input></td></tr>
		<tr><td>Name: </td><td><input type="text" name="galerieName" class="required" maxlength="20"/></td></tr>
		<tr><td>Start: </td><td><input type="text" name="startZeit" id="timepicker1" class="required" minlength="2"/></td></tr>
		<tr><td>Ende: </td><td><input type="text" name="endZeit" id="timepicker2" class="required" minlength="2"/></td></tr>
	</table>
	<br>
	<input type="submit" value="Folie Speichern">
	</form>
	<?php
		
	break;
	
	//Galerie Bearbeiten
	case 6:
	$id = $_GET['id'];

	$sql = "SELECT * FROM folie WHERE intFolie_ID = '$id'";
	
	$result = $db->query($sql);
	
	if($result->num_rows){
		$row = $result->fetch_assoc();
		
		$id = $row['intFolie_ID'];
		$von = formatiereZeitSQL2DE($row['dateTimeVon']);
		$bis = formatiereZeitSQL2DE($row['dateTimeBis']);
		$name = $row['strText1'];
		$path = $row['strPath'];
	echo'
	<h1>GALERIE BEARBEITEN</h1>
	<form id="Form" name="neueFolie" action="?pid=5&&track=8" method="POST" enctype="multipart/form-data">
	<table>
		<tr><td>Pfad: </td><td>'.$path.'</td></tr>
		<tr><td>Name: </td><td><input type="text" name="galerieName" class="required" maxlength="20" value="'.$name.'"/></td></tr>
		<tr><td>Start: </td><td><input type="text" name="startZeit" id="timepicker1" class="required" minlength="2" value="'.$von.'"/></td></tr>
		<tr><td>Ende: </td><td><input type="text" name="endZeit" id="timepicker2" class="required" minlength="2" value="'.$bis.'"/></td></tr>
	</table>
	<br>
	<input type="submit" value="Folie Speichern">
	</form>';
	break;	
}
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

<script type="text/javascript">
//Überprüft ob ein Bild ausgewählt wurde
function checkPic(){
	res=true;
	if(document.neueFolie.bild.value==""){res=false;}
	if(res==false){alert("Bitte ein Bild auswählen!")}
	return res;
}

</script>