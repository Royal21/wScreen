<?php
// +----------------------------------------------------------------------+
// | Copyright (c) 2011 Marco von Gunten <marcovg@gmx.ch> 		  		  |
// +----------------------------------------------------------------------+
// | In dieser Datei wird das Login ausgewertet oder das Logout vollzogen |
// | Track 1 ist für das Login, Track 2 ist für das Logout.				  |
// +----------------------------------------------------------------------+
include("includes/dbConnect.inc.php");

$track = $_GET['track'];

if(isset($track)){

switch($track){

	case 1:

		//Abgesendete Logindaten vom Loginformular
		$username = $_POST['username'];
		$password = $_POST['password'];

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

					$result = ldap_first_entry($ldapconn, $search);

					//Benutzereingaben sind im LDAP vorhanden
					if (!empty($username) && !empty($password) && $result!== false && @ldap_bind($ldapconn, ldap_get_dn($ldapconn, $result), $password)) {

						//sAMAccountName in SESSION['AccountName'] Variable speichern
						$_SESSION['AccountName'] = $username;
						$result = ldap_first_entry($ldapconn, $search);

						//mail in SESSION['mail'] Variable speichern
						$mail_array = ldap_get_values($ldapconn, $result, 'mail');
						$_SESSION['mail'] = $mail_array[0];

						//Vorname in SESSION['vorname'] Variable speicherm
						$name_array = ldap_get_values($ldapconn, $result, 'givenname');
						$_SESSION['vorname'] = $name_array[0];

						//Nachname in SESSION['nachname'] Variable speicherm
						$name_array = ldap_get_values($ldapconn, $result, 'sn');
						$_SESSION['nachname'] = $name_array[0];

						//Überprüfen ob Benutzer auch in Applikations Admin-Datenbank ist
						$sql = "SELECT * FROM admin WHERE `strAccountName` = '$username' && `intIsActive` = '1'";

						$result = $db->query($sql);

						//Eintrag mit diesem usernamen vorhanden
						if($result->num_rows){

							$row = $result->fetch_assoc();
							$_SESSION['adminID'] = $row['intAdmin_ID'];

							$_SESSION['access'] = "granted";
							header('Location:?pid=0');
						}
						//Der Benutzer hat keine Berechtigungen auf der Applikation
						else{
							header("Location:?pid=1&&message=2");
							
						}
					//Falsche Benutzerangaben
					}
					else{
						header("Location:?pid=1&&message=1");
					}
				}

			}
		}
		ldap_unbind($ldapconn);

		break;

	case 2:

		unset($_SESSION['access']);
		header("Location:?pid=0");

		break;

}
}
else{
	header("Location:?pid=0");
}