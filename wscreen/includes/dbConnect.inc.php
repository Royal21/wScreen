<?php
// +----------------------------------------------------------------------+
// | Copyright (c) 2011 Marco von Gunten <marcovg@gmx.ch> 		  		  |
// +----------------------------------------------------------------------+
// | Die dbConnect sorgt für die Verbindung mit der Datenbank			  |
// | ACHTUNG! Zwei Versionen vorhanden!									  |
// | Dieser Version ist für die Datenbank auf dem localhost.				  |
// +----------------------------------------------------------------------+

$db = @new mysqli('localhost', 'root', '', 'bbcnet_ch_wscreen');
if (mysqli_connect_errno()) {
	die('Konnte keine Verbindung zur Datenbank aufbauen: '.mysqli_connect_error().'('.mysqli_connect_errno().')');
}