<?php
// +----------------------------------------------------------------------+
// | Copyright (c) 2011 Marco von Gunten <marcovg@gmx.ch> 		  		  |
// +----------------------------------------------------------------------+
// | In dieser Datei werden alle SQL Inserts oder Deletes ausgeführt.	  |
// | Jegliche Formulare dieser Websites welche auf der Datenbank etwas	  |
// | verändern, leiten auf diese Datei weiter. Diese führt dies dann aus. |
// +----------------------------------------------------------------------+

include('includes/dbConnect.inc.php');

//Mit Hilfe der Get-Variable Track wird heraufgefunden, welche Aufgabe ausgeführt werden muss
$track = $_GET['track'];

if(isset($track)){

//"Jetzt" in Variable speichern
$timestamp = time();
$datum = date("Y-m-d",$timestamp);
$uhrzeit = date("H:i",$timestamp);
$now = $datum." ".$uhrzeit;

switch($track){

	/////////////////////////////
	//Benutzer wird deaktiviert//
	/////////////////////////////
	case 1:
		$id = $_GET['id'];

		//Zählen der Einträge in der Datenbank, falls der letzte, kann er nicht deaktiviert werden
		$sql = "SELECT * FROM admin WHERE `intIsActive` = 1";
		$result = $db->query($sql);
		if (!$result) {
			die('Der Query konnte nicht ausgeführt werden: '.$db->error);
		}

		$i = 0;
		while($row = $result->fetch_assoc()){
			$i++;
		}
		//Falls mehrere Einträge vorhanden sind
		if($i>1){
			$sql = "UPDATE admin SET `intIsActive` = 0 WHERE `intAdmin_ID` = '$id'";
			$result = $db->query($sql);
			if (!$result) {
				die('Der Query konnte nicht ausgeführt werden: '.$db->error);
			}
			//Der Benutzer wurde erfolgreich gelöscht
			$message = 1;
		}
		//Falls nur ein Eintrag vorhanden ist
		else{
			//Der Benutzer kann nicht gelöscht werden! Letzter Eintrag in der Datenbank!
			$message = 2;
		}

		header("Location:?pid=4&&message=$message");

		break;

		///////////////////////////////////
		//Neuer Benutzer wird gespeichert//
		///////////////////////////////////
	case 2:
		//Übergebener Benutzername
		$username = htmlentities($_POST['userName'], ENT_QUOTES, 'utf-8');

		//Daten welche für das LDAP benötigt werden
		// LDAP Server
		$_ldap_server = '172.16.1.4';

		// LDAP User
		$_ldap_user = 'pontos-moodle';

		// LDAP Pass
		$_ldap_pass = 'BioDL2NS';

		// LDAP DN
		$_ldap_dn = 'ou=Berufsbildungscenter,dc=bbcnet,dc=ch';

		$ldapconn = ldap_connect("ldaps://$_ldap_server/");
		//Verbindung zu LDAP-Server erfolgreich
		if ($ldapconn !== false) {
			//LDAP-Bind erfolgreich
			if (ldap_bind($ldapconn, $_ldap_user, $_ldap_pass)) {

				$search = ldap_search($ldapconn, $_ldap_dn, "sAMAccountName=$username");
				//Eintrag mit $username im LDAP gefunden
				if ($search !== false) {
					//Benutzereingaben sind im LDAP vorhanden
					if (!empty($username) && $result!== false && @ldap_bind($ldapconn, ldap_get_dn($ldapconn, $result))) {

						$result = ldap_first_entry($ldapconn, $search);
						//Vorname in SESSION['vorname'] Variable speicherm
						$name_array = ldap_get_values($ldapconn, $result, 'givenname');
						$vorname = utf8_encode($name_array[0]);
							
						//Nachname in SESSION['nachname'] Variable speicherm
						$name_array = ldap_get_values($ldapconn, $result, 'sn');
						$nachname = utf8_encode($name_array[0]);

						//Wenn die Variable leer ist, existiert der angegebene Benutzer nicht im Active Directory
						if(!$nachname==""){

							//Überprüfen ob der Benutzer schon in der Datenbank ist
							$sql = "SELECT * FROM admin WHERE `strAccountName` = '$username'AND `intIsActive` = '1'";

							$result = $db->query($sql);

							//Eintrag ist noch nicht vorhanden
							if(!$result->num_rows){

								//Überprüfen ob der Benutzer schon in der Datenbank ist, aber isActive auf 0 hat
								$sql = "SELECT * FROM admin WHERE `strAccountName` = '$username' AND `intIsActive` = '0'";

								$result = $db->query($sql);

								//Eintrag ist bereits vorhanden, isActive wird wieder auf 1 gesetzt
								if($result->num_rows){

									$sql = "UPDATE admin SET `intIsActive` = 1 WHERE `strAccountName` = '$username'";
									$result = $db->query($sql);
									if (!$result) {
										die('Der Query konnte nicht ausgeführt werden: '.$db->error);
									}
								}
								//Eintrag ist noch nicht vorhanden, Benutzer wird neu erstellt
								else{
									$sql = "INSERT INTO admin(`strAccountName`, `intIsActive`, `strVorname`, `strNachname`) VALUES('$username','1','$vorname', '$nachname')";
									$result = $db->query($sql);
									if (!$result) {
										die('Der Query konnte nicht ausgeführt werden: '.$db->error);
									}

								}

							}
							//Kein Eintrag im AD mit diesem usernamen vorhanden
							else{
								//Der Benutzer ist bereits in der Applikation vorhanden!
								$message = 4;
							}
						}
						else{
							//Der Benutzer ist nicht im Active Directory vorhanden!
							$message = 3;
						}
					}

				}
			}
		}
		ldap_unbind($ldapconn);

		header("Location: ?pid=4&&message=$message");

		break;

		////////////////////////////////////////
		//Neue Template-Folie wird gespeichert//
		////////////////////////////////////////
	case 3:
		//Werte abfangen und in Variablen speichern
		$text1 = htmlentities($_POST['ueberschrift'], ENT_QUOTES, 'utf-8');
		$text2 = nl2br(htmlentities($_POST['text'], ENT_QUOTES, 'utf-8'));
		$empfang = $_POST['empfang'];
		$startZeit = formatiereZeitDE2SQL($_POST['startZeit']);
		$endZeit = formatiereZeitDE2SQL($_POST['endZeit']);
		$adminID = $_SESSION['adminID'];

		//Falls der Termin in der Vergangenheit liegt
		if($endZeit>$now){
			//Falls die Endzeit nach der Startzeit ist
			if($startZeit < $endZeit){
				//Prüfen, ob die Zeiten in der Datenbank bereits besetzt sind
				if(checkBetween($startZeit, $endZeit)){
					$sql = "INSERT INTO folie(`dateTimeVon`, `dateTimeBis`, `strText1`, `strText2`, `intAdmin_ID`, `intTemplate_ID`) VALUES('$startZeit','$endZeit','$text1', '$text2', '$adminID','$empfang')";

					$result = $db->query($sql);
					if($result){
						$message = 1;
					}
				}
				else{
					//Die Zeigt ist bereits belegt
					$message = 8;
				}
			}
			else{
				//Startzeit liegt hinter der Endzeit
				$message=2;
			}
		}
		else{
			//Termin liegt in der Vergangeneheit
			$message = 9;
		}
		header("Location:?pid=0&&message=$message");
		break;

		///////////////////////
		//Folie wird gelöscht//
		///////////////////////
	case 4:
		$id = $_GET['id'];
		$path = $_GET['path'];

		//Falls eine Bild-Folie
		if($path!=""){
			//Überprüfen ob noch andere Einträge das selbe Bild benötigen
			$sql = "SELECT COUNT(*) FROM folie WHERE `strPath` = '$path'";
			$result = $db->query($sql);
			$row = $result->fetch_assoc();
			//Falls Bild nur von diesem Eintrag verwendet wurde
			if($row[0]<=1){
				//Bild im Dateisystem löschen
				unlink($path);
			}
		}

		$sql="DELETE FROM `folie` WHERE `intFolie_ID` = '$id'";

		$result = $db->query($sql);

		if($result){
			//Erfolgreich gelöscht
			$message = 3;
		}

		header("Location:?pid=0&&message=$message");
		break;

		/////////////////////////
		//Update Template-Folie//
		/////////////////////////
	case 5:
		$id = $_GET['id'];
		$text1 = htmlentities($_POST['ueberschrift'], ENT_QUOTES, 'utf-8');
		$text2 = nl2br(htmlentities($_POST['text'], ENT_QUOTES, 'utf-8'));
		$empfang = $_POST['empfang'];
		$startZeit = formatiereZeitDE2SQL($_POST['startZeit']);
		$endZeit = formatiereZeitDE2SQL($_POST['endZeit']);
		$adminID = $_SESSION['adminID'];

		//Falls der Termin in der Vergangenheit liegt
		if($endZeit>$now){
			//Falls die Endzeit nach der Startzeit ist
			if($startZeit < $endZeit){
				//Prüfen, ob die Zeiten in der Datenbank bereits besetzt sind
				if(checkBetween($startZeit, $endZeit, $id)){
					$sql="UPDATE folie SET `dateTimeVon` = '$startZeit', `dateTimeBis` = '$endZeit', `strText1` = '$text1', `strText2` = '$text2', `intAdmin_ID` = '$adminID', `intTemplate_ID` = '$empfang' WHERE `intFolie_ID` = $id";

					$result = $db->query($sql);
					if (!$result) {
						die('Der Query konnte nicht ausgeführt werden: '.$db->error);
					}
					//Erfolgreicher Update
					$message = 4;
				}

				else{
					//Die Zeigt ist bereits belegt
					$message = 8;
				}
			}
			else{
				//Startzeit liegt hinter der Endzeit
				$message = 2;
			}
		}
		else{
			//Termin liegt in der Vergangenheit
			$message = 9;
		}

		header("Location:?pid=0&&message=$message");

		break;

		////////////////////////////////////
		//Neue Bild-Folie wird gespeichert//
		////////////////////////////////////
	case 6:

		//Werte abfangen und in Variablen speichern
		$startZeit = formatiereZeitDE2SQL($_POST['startZeit']);
		$endZeit = formatiereZeitDE2SQL($_POST['endZeit']);
		$bild_name = $_FILES['bild']['name'];
		$adminID = $_SESSION['adminID'];

		//Falls der Termin in der Vergangenheit liegt
		if($endZeit>$now){
			//Falls die Endzeit nach der Startzeit ist
			if($startZeit < $endZeit){
				//Prüfen, ob die Zeiten in der Datenbank bereits besetzt sind
				if(checkBetween($startZeit, $endZeit)){
					//Falls das Bild fehlerfrei auf dem Server gespeichert wurde
					if(uploadPicture()){

						$bild_location = $_SESSION['bild_location'];
						$sql = "INSERT INTO folie(`dateTimeVon`, `dateTimeBis`, `strPath`, `intAdmin_ID`, `intTemplate_ID`) VALUES('$startZeit','$endZeit','$bild_location', '$adminID','0')";

						$result = $db->query($sql);
						if($result){
							//Folie wurde erfolgreich gespeichert
							$message = 1;
						}
					}
					else{
						$message = 5;
					}
				}
				else{
					//Die Zeigt ist bereits belegt
					$message = 8;
				}
			}
			else{
				//Endzeit ist vor der Startzeit gewählt
				$message = 2;
			}
		}
		else{
			//Termin liegt in der Vergangenheit
			$message = 9;
		}
		header("Location:?pid=0&&message=$message");
		break;

		/////////////////////
		//Update Bild-Folie//
		/////////////////////
	case 7:
		$id = $_GET['id'];
		$startZeit = formatiereZeitDE2SQL($_POST['startZeit']);
		$endZeit = formatiereZeitDE2SQL($_POST['endZeit']);
		$adminID = $_SESSION['adminID'];

		//Falls der Termin in der Vergangenheit liegt
		if($endZeit>$now){
			//Falls die Endzeit nach der Startzeit ist
			if($startZeit < $endZeit){
				//Prüfen, ob die Zeiten in der Datenbank bereits besetzt sind
				if(checkBetween($startZeit, $endZeit, $id)){
					$sql="UPDATE folie SET `dateTimeVon` = '$startZeit', `dateTimeBis` = '$endZeit', `intAdmin_ID` = '$adminID' WHERE `intFolie_ID` = $id";

					$result = $db->query($sql);
					if (!$result) {
						die('Der Query konnte nicht ausgeführt werden: '.$db->error);
					}
					//Erfolgreicher Update
					$message = 4;
				}
				else{
					//Die Zeigt ist bereits belegt
					$message = 8;
				}
			}
			else{
				//Startzeit liegt hinter der Endzeit
				$message = 2;
			}
		}
		else{
			//Der Termin liegt in der Vergangenheit
			$message = 9;
		}

		header("Location:?pid=0&&message=$message");

		break;

		/////////////////////////////////
		//Neue Galerie wird gespeichert//
		/////////////////////////////////
	case 8:
			
		//Werte abfangen und in Variablen speichern
		$startZeit = formatiereZeitDE2SQL($_POST['startZeit']);
		$endZeit = formatiereZeitDE2SQL($_POST['endZeit']);
		$datei = $_FILES['zip']['name'];
		$galerieName = $_POST['galerieName'];
		$adminID = $_SESSION['adminID'];
		$upload = uploadZip();
		$datei_location = "upload/galerie/".$datei;
		$galerie_location = str_replace(".zip", "",$datei_location);

		//Falls der Termin in der Vergangenheit liegt
		if($endZeit>$now){
			//Falls die Endzeit nach der Startzeit ist
			if($startZeit < $endZeit){
				//Prüfen, ob die Zeiten in der Datenbank bereits besetzt sind
				if(checkBetween($startZeit, $endZeit)){
					//Zip erfolgreich hochgeladen
					if($upload=="true"){
						require_once('includes/pclzip.lib.php');
						$unzip = unzip();
						if($unzip=="true"){
							$resized = resizeImagesInFolder($galerie_location);
							if($resized =="true"){
							$sql = "INSERT INTO folie(`dateTimeVon`, `dateTimeBis`, `strText1`, `strPath`, `intAdmin_ID`, `intTemplate_ID`) VALUES('$startZeit','$endZeit', '$galerieName', '$galerie_location', '$adminID','2')";

							$result = $db->query($sql);
							if($result){
								//Folie wurde erfolgreich gespeichert
								$message = 10;
							}
							}
							else{
								//Resize fehlgeschlagen
							}
						}
						else{
							$message = 11;
						}
					}
					//falscher Dateityp
					else if($upload=="1"){
						$message = 12;
					}
					//invalide Datei
					else if($upload=="2"){
						$message = 13;
					}
				}
				else{
					//Die Zeigt ist bereits belegt
					$message = 8;
				}
			}
			else{
				//Endzeit ist vor der Startzeit gewählt
				$message = 2;
			}
		}
		else{
			//Termin liegt in der Vergangenheit
			$message = 9;
		}
		unlink($datei_location);
		header("Location:?pid=0&&message=$message");

		break;
		
	//Galerie löschen
	case 9:
		$id = $_GET['id'];
		$path = $_GET['path'];

		//Überprüfen ob noch andere Einträge das selbe Bild benötigen
		$sql = "SELECT COUNT(*) AS counter FROM folie WHERE `strPath` = '$path'";
		$result = $db->query($sql);
		$row = $result->fetch_assoc();
		//Falls Bilder nur von diesem Eintrag verwendet werden
		if($row['counter']<=1){
			//Bild im Dateisystem löschen
		rrmdir($path);
		}


		$sql="DELETE FROM `folie` WHERE `intFolie_ID` = '$id'";

		$result = $db->query($sql);

		if($result){
			//Erfolgreich gelöscht
			$message = 3;
		}

		header("Location:?pid=0&&message=$message");
		break;

}
}
else{
	header("Location:?pid=0");
}

//Formatiert die Zeit aus dem deutschen ins DB-Format
function formatiereZeitDE2SQL ($zeit){
	//Datum und Zeit trennen
	$teile = explode(" ", $zeit);
	$date = $teile[0];
	$time = $teile[1];

	//Datum in Einzelteile Jahr, Monat und Tag teilen und in dateNew neu anordnen
	$teile = explode(".", $date);
	$dateNew = $teile[2]."-".$teile[1]."-".$teile[0];

	//Neues datum mit der Zeit zusammensetzen
	$zeit = $dateNew." ".$time.":00";

	return $zeit;
}

//Datei Upload
function uploadPicture(){

	//Standard Werte definieren
	define('MAX_SIZE', "3145728");	// Maximal 3MB
	define('UPLOAD_DIR',"upload/images/");//Ordner in dem die Bilder gespeichert werden

	//Bildwerte abfangen und speichern
	$bild_name = $_FILES['bild']['name'];
	$bild_tmp = $_FILES['bild']['tmp_name'];
	$bild_size = $_FILES['bild']['size'];
	$bild_type = $_FILES['bild']['type'];

	//Allfällige Punkte aus String löschen damit extension richtig festgestellt wird
	$bild_name = deletePoints($bild_name);

	$bild_ext = strtolower(substr($bild_name, strpos($bild_name,".")+1)); //toLower um beim überprüfen keine Probleme zu haben

	//Überprüfen ob Datei nicht leer ist und keine Fehler hat
	if((!empty($_FILES['bild'])) && ($_FILES['bild']['error'] == 0)){
		if(!($bild_ext == "jpg" || $bild_ext == "jpeg" || $bild_ext == "gif" || $bild_ext == "png")){
			//Datei-Typ ist nicht korrekt
			return false;
		}
		if($bild_size > MAX_SIZE){
			//Datei ist zu gross
			return false;
		}
	}
	else{
		//Keine oder fehlerhafte Datei
		return false;
	}

	//Alle Vorgaben korrekt
	if(!isset($message)){
		//Pfad für Datei welche auf Server liegen wird und in DB gespeichert wird
		$bild_location = UPLOAD_DIR.$bild_name;

		//Datei auf den Server kopieren
		move_uploaded_file($bild_tmp, $bild_location);

		$sizes = getimagesize($bild_location);
		$width = $sizes[0];
		$height = $sizes[1];

		//Falls die Grösse nicht der vorgegebenen entspricht
		if($width != "1280" && $height != "768"){
			//Bild mit Hilfe der SimpleImage Klasse auf die benötigte grösse 1280x768px anpassen
			include('includes/simpleImage.php');
			$image = new SimpleImage();
			$image->load($bild_location);
			$image->resize(1280,768);
			$image->save($bild_location);
			$_SESSION['bild_resize'] = true;
			//Upload Ordner in Session speichern, um später für DB Query wieder abfragen zu können
			$_SESSION['bild_location'] = $bild_location;
			unlink($temp_bild_location);
		}
		else{
			$_SESSION['bild_resize'] = false;
			//Upload Ordner in Session speichern, um später für DB Query wieder abfragen zu können
			$_SESSION['bild_location'] = $bild_location;
		}

		//Bildupload erfolgreich
		return true;
	}
	else{
		//Bildupload fehlgeschlagen
		return false;
	}
}

function uploadZip(){

	define('UPLOAD_DIR',"upload/zip/");

	//Bildwerte abfangen und speichern
	$zip_name = $_FILES['zip']['name'];
	$zip_tmp = $_FILES['zip']['tmp_name'];
	$zip_type = $_FILES['zip']['type'];
	if((!empty($_FILES['zip'])) && ($_FILES['zip']['error'] == 0)){
		if($zip_type == "application/x-zip"){
			//Speicherort der Zip-Datei
			$location = UPLOAD_DIR.$zip_name;

			//Zip-Datei auf den Server kopieren
			move_uploaded_file($zip_tmp, $location);
			return "true";
		}
		else{
			return "1";
		}
	}
	else{
		return "2";
	}
}

function unzip(){
	define('ZIP_DIR', "upload/zip/");
	define('GALERIE_DIR', "upload/galerie/");
	$zipArchive = $_FILES['zip']['name'];
	$zipLocation = ZIP_DIR.$zipArchive;

	$archive = new PclZip($zipLocation);
	if ($archive->extract(PCLZIP_OPT_PATH, GALERIE_DIR) == 0) {
		return "Error : ".$archive->errorInfo(true);
	}
	else{
		return "true";
	}
}

function resizeImagesInFolder($folder){
	//Time Limit vergrössern, da für viele Bilder viel Zeit in Anspruch genommen wird.
	set_time_limit (120);
	include('includes/simpleImage.php');
	$image = new SimpleImage();
	if($handle = opendir($folder)){
		while(false !== ($file = readdir($handle))){
			if ($file != "." && $file != "..") {
			$bild_location = $folder."/".$file;

			$info = getimagesize($bild_location);
			$width = $info[0];
			$height = $info[1];
			echo "<b>".$bild_location."</b><br>";
			echo'Old width:'. $width.'<br>height:'.$height."<br>";
						
			if($height > 700){
				$image->load($bild_location);
				$image->resizeToHeight(700);
				$image->save($bild_location);
			}
			
			if($width > 1200){
				$image->load($bild_location);
				$image->resizeToWidth(1200);
				$image->save($bild_location);
			}

			$info = getimagesize($bild_location);
			$width = $info[0];
			$height = $info[1];
			echo'New width:'. $width.'<br>height:'.$height."<br><br><br>";
		}
		}
	}
	return true;
}

//Löscht alle Punkte bis auf den Letzten aus dem String
function deletePoints($bild_name){
	$positionletzterpunkt = strrpos($bild_name, '.');
	//'#' als temporäres Ersatzzeichen für den letzten Punkt
	$bild_name = substr_replace($bild_name, '#', $positionletzterpunkt, 1);
	// Ersetzen der anderen Punkte
	$bild_name = str_replace('.', '', $bild_name);
	//temporären Platzhalter '#' wieder entfernen
	$bild_name = str_replace('#', '.', $bild_name);
	return $bild_name;
}

//Prüfen, ob die Zeiten in der Datenbank bereits besetzt sind
function checkBetween($startZeit, $endZeit, $id){
	include ('includes/dbConnect.inc.php');

	//startZeit überprüfen
	//Ist die Startzeit während zwei bereits existierenden Zeiten gewählt?
	$sql = "SELECT `intFolie_ID` FROM folie WHERE '$startZeit' BETWEEN `dateTimeVon` AND `dateTimeBis`";
	$result = $db->query($sql);

	if (!$result) {
		die('Der Query konnte nicht ausgeführt werden: '.$db->error);
	}
	//Falls Resultate zurückgegeben werden, überprüfen, ob es die zu bearbeitende Folie ist
	if($result->num_rows){
		$row = $result->fetch_assoc();
		$folieID = $row['intFolie_ID'];
		//Ausnahme, wenn zurückgelieferter Eintrag die aktuell zu bearbeitende Folie ist
		if($folieID != $id){
			$_SESSION['folieBesetzt'] = $folieID;
			return false;
		}
	}

	//startZeit überprüfen
	//Ist die EndZeit während zwei bereits existierenden Zeiten gewählt?
	$sql = "SELECT `intFolie_ID` FROM folie WHERE '$endZeit' BETWEEN `dateTimeVon` AND `dateTimeBis`";
	$result = $db->query($sql);

	if (!$result) {
		die('Der Query konnte nicht ausgeführt werden: '.$db->error);
	}
	//Falls Resultate zurückgegeben werden, überprüfen, ob es die zu bearbeitende Folie ist
	if($result->num_rows){
		$row = $result->fetch_assoc();
		$folieID = $row['intFolie_ID'];

		//Ausnahme, wenn zurückgelieferter Eintrag die aktuell zu bearbeitende Folie ist
		if($folieID != $id){
			$_SESSION['folieBesetzt'] = $folieID;
			return false;
		}
	}

	//startZeit überprüfen
	//Ist die EndZeit während zwei bereits existierenden Zeiten gewählt?
	$sql = "SELECT `intFolie_ID` FROM folie WHERE `dateTimeVon` BETWEEN '$startZeit' AND '$endZeit'";
	$result = $db->query($sql);

	if (!$result) {
		die('Der Query konnte nicht ausgeführt werden: '.$db->error);
	}
	//Falls Resultate zurückgegeben werden, überprüfen, ob es die zu bearbeitende Folie ist
	if($result->num_rows){
		$row = $result->fetch_assoc();
		$folieID = $row['intFolie_ID'];

		//Ausnahme, wenn zurückgelieferter Eintrag die aktuell zu bearbeitende Folie ist
		if($folieID != $id){
			$_SESSION['folieBesetzt'] = $folieID;
			return false;
		}
	}

	//startZeit überprüfen
	//Ist die EndZeit während zwei bereits existierenden Zeiten gewählt?
	$sql = "SELECT `intFolie_ID` FROM folie WHERE `dateTimeBis` BETWEEN '$startZeit' AND '$endZeit'";
	$result = $db->query($sql);

	if (!$result) {
		die('Der Query konnte nicht ausgeführt werden: '.$db->error);
	}
	//Falls Resultate zurückgegeben werden, überprüfen, ob es die zu bearbeitende Folie ist
	if($result->num_rows){
		$row = $result->fetch_assoc();
		$folieID = $row['intFolie_ID'];

		//Ausnahme, wenn zurückgelieferter Eintrag die aktuell zu bearbeitende Folie ist
		if($folieID != $id){
			$_SESSION['folieBesetzt'] = $folieID;
			return false;
		}
	}
	return true;
}

function rrmdir($dir) {
   if (is_dir($dir)) {
     $objects = scandir($dir);
     foreach ($objects as $object) {
       if ($object != "." && $object != "..") {
         if (filetype($dir."/".$object) == "dir") rrmdir($dir."/".$object); else unlink($dir."/".$object);
       }
     }
     reset($objects);
     rmdir($dir);
   }
 } 