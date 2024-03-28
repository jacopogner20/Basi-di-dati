<?php
session_start();
//mi connetto al database
$mysqli = new mysqli('localhost', 'root', '', 'progettoBdb2.0');
if ($mysqli->connect_error){
	die('Errore di connessione(' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
}
//estraggo i dati dal post
extract($_POST);
//se l'utente inserisce solo la foto del post senza titolo o testo mostro il messaggio di errore
if(isset($_FILES['file'])){
	if ($_FILES['file']['name'] != "") {
		if ($_POST["titolo"] == "" || $_POST["testo"] == "" ) {
			$_SESSION["eroreCaricamentoImmagine"] = "Compila tutti i campi!";
			header("location:blog.php?id=$idBlog");
			exit();
		}
	}
}
//variabile per la data e l'ora
$dataeora =  date("d/m/Y g:i:s A");
//se i campi relativi al testo e al titolo del post sono corretti lo aggiungo al database
if(isset($_POST["titolo"], $_POST["testo"], $_POST["idblog"]) && $_POST["titolo"] != "" && $_POST["testo"] != ""){
	extract($_POST);
	$titolo = htmlspecialchars($_POST["titolo"]);
	$testo = htmlspecialchars($_POST["testo"]);
	$idBlog = $_POST["idblog"];
	//id dell'utente che scrive il post 
	$idUtente = $_SESSION["idutente"];
	// query che inserisce il post nella tabella post
	$queryInsTit = $mysqli -> prepare("INSERT INTO post(idblog, idautore, titolo, dataeora, testo) VALUES(?,?,?,?,?)");
	$queryInsTit -> bind_param('sssss', $idBlog, $idUtente, $titolo, $dataeora, $testo);
	$queryInsTit -> execute();
	$resInsTit = $queryInsTit -> get_result();
} else {
	$_SESSION["eroreCaricamentoImmagine"] = "Compila tutti i campi!";
	header("location:blog.php?id=$idBlog");
}
//se il commento è scritto correttamente lo aggiungo alla tabella commenti
if(isset($_POST["commento"]) && $_POST["commento"] != "") {
	$commento = htmlspecialchars($_POST["commento"]);
	$post = $_POST["idpost"];
	$autore = $_POST["idautore"];
	//query che inserisce il commento
	$queryInsertCommento = $mysqli -> prepare("INSERT INTO commenti(idpost, idautore, dataeora, testo) VALUES (?,?,?,?)");
	$queryInsertCommento -> bind_param('ssss', $post, $autore, $dataeora, $commento);
	$queryInsertCommento -> execute();
	$resInsertCommento = $queryInsertCommento -> get_result();
}  
?>