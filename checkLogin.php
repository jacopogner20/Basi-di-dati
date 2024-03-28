<?php
session_start();
//mi connetto al database
include('connetti.php');
//estraggo i dati
$mail = $_POST["email"];
$password = $_POST["passwordLog"];
//preparo la query
$sql = $mysqli->prepare("SELECT * FROM utente WHERE mail = ? AND password = ?");
$sql -> bind_param('ss', $mail, $password);
$sql -> execute();
$result = $sql -> get_result();
//se il nickname esiste invio l'utente alla home
if ($result -> num_rows > 0){
	$row = $result->fetch_assoc();
	$_SESSION["username"] = $row["nickname"];
	$_SESSION["idutente"] = $row["idutente"];
	header("location: home2.php");
//se l'utente inserisce una mail o una password non corretti mostro un messaggio di errore
} else {
	$_SESSION["messaggio"] = "Email o password non corretti";
	header('location: login.php');
}
?>