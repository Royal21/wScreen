/*
 * In dieser Datei werden zuerst die Elemente für die älteren Versionen des IE initialisiert
 * Anschliessende werden die einzelnen verwendeten Librarys initialisiert
 */


//HTML5 Elemente für IE Initialisieren 
document.createElement("header");
document.createElement("nav");
document.createElement("footer");


$(function() {
	// Initialisieren der Uniform Library für Styling der Eingabeformen
	$("input, radio, textarea, select, button").uniform();

	// Initialisieren der validate Library für die Überprüfung der Eingabeformen
	$("#Form").validate();

	// Initialisieren der Timepicker Library
	$('#timepicker1').datetimepicker();

	$('#timepicker2').datetimepicker();

	// Initialisieren der Slideshow
	$('#slideshow').rsfSlideshow();
});