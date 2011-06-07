<?php
// +----------------------------------------------------------------------+
// | Copyright (c) 2011 Marco von Gunten <marcovg@gmx.ch> 		  		  |
// +----------------------------------------------------------------------+
// | Auf dieser Seite wird das Login Formular angezeigt.				  |
// | Die Weiterleitung erfolgt nach dem drücken des Submit Buttons.		  |
// | 																	  |
// +----------------------------------------------------------------------+

include('includes/header.inc.php');

//Falls bereits eingeloggt, wird Logout Button angezeigt
if($_SESSION['access'] == "granted"){
?>
<h1>LOGOUT</h1>
Wirklich ausloggen?
<form name="logout" action="?pid=2&&track=2" method="POST">
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
<input type="submit" name="logout" value="Ja"></form>
<?php
}
//Wenn noch nicht eingeloggt, wird Login Formular angezeigt
else{
	$message = $_GET['message'];
	echo'<h1>LOGIN</h1>';
	//Falls bei vorherigem Versuch falsche angaben gemacht wurden,
	//Wird fehlermeldung angezeigt
	switch($message){
		case 1:
			echo'<div id="red">Login fehlgeschlagen! Bitte überprüfen Sie Ihre Benutzerdaten!<br><br></div>';
			break;
		case 2:
			echo'<div id="red">Login fehlgeschlagen! Sie haben keine Berechtigungen auf der Applikation!<br><br></div>';
			break;			
	}
?>
<form id="Form" name="login" action="?pid=2&&track=1" method="POST">

<table>
	<tr>
		<td>Benutzername:</td>
		<td><input type="text" name="username" class="required" minlength="2"></td>
	</tr>
	<tr>
		<td>Passwort:</td>
		<td><input type="password" name="password" class="required" minlength="2"></td>
	</tr>
	<tr>
		<td><input type="submit" name="login" value="Login"></td>
	</tr>
</table>
</table>
</form>
<script type="text/javascript" event="onload()">
document.login.username.focus();
</script>
<?php
}
include('includes/footer.inc.php');
?>