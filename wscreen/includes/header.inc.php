<!DOCTYPE HTML>
<html>
<head>

<!--+-----------------------------------------------------------------------+-->
<!--| Der header bildet die												    |-->
<!--| Grundlage jeder Anzeige-Seite. Hier werden die benötigten			    |-->
<!--| HTML Vorgaben generiert. Ausserdem werden benötigte externe CSS und   |-->
<!--| JavaScript Dateien geladen. Auch die benötigten Div's werden geöffnet.|-->
<!--+-----------------------------------------------------------------------+-->

<meta charset="utf-8"/>
<meta http-equiv="cache-control" content="no-cache">
<title>Verwaltungstool Willkommensbildschirm</title>
<link href="css/styleVerwaltung.css" rel="stylesheet" type="text/css" />
<link href="css/uniform.default.css" rel="stylesheet"  type="text/css" media="screen"/>
<link href="css/jquery-ui.custom.css" rel="stylesheet"  type="text/css" media="screen"/>
<link rel="icon" href="css/images/favicon.png" type="image/x-icon">
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
<script src="js/jquery.uniform.js" type="text/javascript"></script>
<script src="js/jquery.validate.js" type="text/javascript"></script>
<script src="js/jquery-ui-1.8.10.custom.min.js" type="text/javascript"></script>
<script src="js/jquery.timepicker.js" type="text/javascript"></script>
<script src="js/functions.js" type="text/javascript"></script>
</head>
<body>
<header>
<!--Hier wird der Header angezeigt, welche im CSS Stylesheet definiert ist-->
</header>
<nav>
<?php 
//Menu wird nur angezeit falls man eingeloggt ist
if($_SESSION['access'] =="granted"){
?>
<a class="folien" href="?pid=0">Folien</a>
<a class="benutzer" href="?pid=4">Benutzer</a>
<a class="logout" href="?pid=1">Logout</a>
<?php 
}
?>
</nav>
<div id="content-top">
</div>
<div id="content">
<div id="text-content">