<?php
	//mi connetto al database
	$mysqli = new mysqli('localhost', 'root', '', 'progettoBdb2.0');
	if ($mysqli->connect_error){
		die('Errore di connessione(' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
	} 
?>