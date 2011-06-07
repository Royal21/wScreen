<?php


session_start();

if($_SESSION['access'] == "granted"){
$section['0'] = "admin/folien.php";
$section['4'] = "admin/benutzer.php";
$section['5'] = "admin/sqlWrite.php";
$section['6'] = "admin/editFolie.php";
}

else{
$section['0'] = "show.php";
}

$section['1'] = "login.php";
$section['2'] = "validate.php";

if(isset($_GET['pid'], $section[$_GET['pid']])){
	include $section[$_GET['pid']];
}
else{
	include $section['0'];
}