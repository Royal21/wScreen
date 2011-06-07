<?php
// +----------------------------------------------------------------------+
// | Copyright (c) 2011 Marco von Gunten <marcovg@gmx.ch> 		  		  |
// +----------------------------------------------------------------------+
// | Auf dieser Seite können die Benutzer verwaltet werden.				  |
// | Es können Administratoren hinzugefügt oder gelöscht werden.		  |
// | Beim löschen werden die Einträge nicht aus der DB entfernt, sondern  |
// | nur auf isActive = 0 gesetzt.										  |
// +----------------------------------------------------------------------+

include('includes/dbConnect.inc.php');
include('includes/header.inc.php');

$message = $_GET['message'];

switch($message){
	//Benutzer gelöscht
	case 1:
		$message = "<div id=red>Der Benutzer wurde erfolgreich gelöscht!</div>";
		break;
		//Letzter aktiver Benutzer in der Datenbank
	case 2:
		$message = "<div id=red>Der Benutzer kann nicht gel&ouml;scht werden! Letzter Eintrag in der Datenbank!</div>";
		break;
		//Benutzer nicht im AD vorhanden
	case 3:
		$message = "<div id=red>Der Benutzer ist nicht im Active Directory vorhanden!</div>";
		break;
		//Benutzer bereits in der Datenbank vorhanden
	case 4:
		$message = "<div id=red>Der Benutzer ist bereits in der Applikation vorhanden!</div>";
		break;
}
?>

<h1>BENUTZER VERWALTUNG</h1>
<?php
$sql = "SELECT `intAdmin_ID`, `strAccountName`, `intIsActive`, `strVorname`, `strNachname` FROM admin WHERE `intIsActive` = '1' GROUP BY `strAccountName`";

$result = $db->query($sql);

//Falls kein Resultat zurückerhalen
if(!$result){
	die("Der Query konnte nicht ausgeführt werden: ".$db->error);
}

//Falls Daten aus der DB erhalten, werden diese in Variablen gespeichert
if($result->num_rows){
	$i = 0;
	while($row = $result->fetch_assoc()){
		$id[$i] = $row['intAdmin_ID'];
		$accountName[$i] = $row['strAccountName'];
		$fullName[$i] = $row['strVorname']." ".$row['strNachname'];
		$i++;
	}
	//Vorhandene Einträge werden in einer Tabelle ausgegeben
	echo'
<h2>VERWALTEN</h2>
	';
	if($message != ""){
		echo $message.'<br>';
	}
	echo'
<table>
<tr><th>Benutzername</th><th>Name</th></tr>';
	$i=0;
	while($i<sizeof($id)){
		echo'<tr><td>'.$accountName[$i].'</td><td>'.$fullName[$i].'</td><td><a href="?pid=5&&track=1&&id='.$id[$i].'" onClick="return confirm(\'Sind Sie sicher, dass Sie diesen Benutzer löschen wollen?\')"><img src="css/images/trash.png"></a></td></tr>';
		$i++;
	}
	echo'</table>';
}
//Formular um neuen Administrator hinzuzufügen
echo'
<h2>NEUER BENUTZER</h2>
<form id="Form" name="newUser" action="?pid=5&&track=2" method="POST">
<input type="text" name="userName" class="required" minlength="2"></input><br>
<input type="submit" name="abschicken" value="hinzuf&uuml;gen"></input>
</form>';
include('includes/footer.inc.php');
?>