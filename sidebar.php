<html>
	<link rel="stylesheet" href="css/tables-min.css">

	<!-- jQuery & jQuery UI + theme (required) -->
	<link href="css/jquery-ui.css" rel="stylesheet">
	<link href="css/jPicker.css" rel="stylesheet">
	<link href="css/jPicker-1.1.6.min.css" rel="stylesheet">
	<link href="css/jPicker-1.1.6.css" rel="stylesheet">
	<script src="js/jquery.js"></script>
	<script src="js/jquery-ui.min.js"></script>
	<script type="text/javascript" src="js/jpicker-1.1.6.min.js"></script>
	<script src="js/custom.js"></script>
	<!-- keyboard widget css & script (required) -->
	<link href="css/keyboard.css" rel="stylesheet">
	<link href="css/pure-release-0.5.0/pure-min.css" rel="stylesheet">
	<link href="css/font-awesome-4.2.0/css/font-awesome.min.css" rel="stylesheet">
	<script src="js/jquery.keyboard.js"></script>

	<!-- keyboard extensions (optional) -->
	<script src="js/jquery.mousewheel.js"></script>


<?php

function menu(){
	print '<table style="width:100%;"><td valign="top">';
	print '<td><a href="cassa.php"><button name="id" class="pure-button" style="width:100%;">Cassa</button></a></td>';
	print '<td><a href="articoli.php"><button name="id" class="pure-button" style="width:100%;">Articoli</button></a></td>';
	print '<td><a href="ordini.php"><button name="id" class="pure-button" style="width:100%;">Ricezione Ordini</button></a></td>';
	print '<td><button class="pure-button" style="width:100%;" onclick="window.open(\'cucina.php\',\'mywin\');">Cucina</button></td>';
	print '</table>';
}
?>
