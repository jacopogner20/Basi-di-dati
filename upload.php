<?php
//mi connetto al database
session_start();
$mysqli = new mysqli('localhost', 'root', '', 'progettoBdb2.0');
if ($mysqli->connect_error){
	die('Errore di connessione(' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
}
$idBlog = $_GET["id"];
//prendo l'ultimo post pubblicato nel blog
$queryIdPost = "SELECT max(idpost) as id from post where idblog = '$idBlog'";
$resIdPost = $mysqli -> query($queryIdPost);
$rowIdPost = $resIdPost -> fetch_assoc();
$idPostFoto = $rowIdPost['id'];
//estraggo i dati del post
extract($_POST);
$testo = $_POST["post"];
$titolo = $_POST["titoloPost"];
//se non sono settati tutti i campi mostro il messaggio di errore
if ($_FILES['file']['name'] != "") {
	if ($testo == "" && $titolo == "") {
		$_SESSION["erroreCaricamentoImmagine"] = "Compila tutti i campi!";
		header("location:blog.php?id=$idBlog");
		exit();
	}
}
if ($fileName = $_FILES['file']['name'] != "") {
	$file = $_FILES['file'];
	$fileName = $_FILES['file']['name'];
	$fileTmpName = $_FILES['file']['tmp_name'];
	$fileSize = $_FILES['file']['size'];
	$fileError = $_FILES['file']['error'];
	$fileType = $_FILES['file']['type'];
	$fileExt = explode('.', $fileName);
	$fileActualExt = strtolower(end($fileExt));
	$allowed = array('jpg', 'jpeg','png', 'pdf');
	if (!in_array($fileActualExt, $allowed)) {
		$_SESSION["erroreCaricamentoImmagine"] = "Non puoi caricare questo tipo di file";
		header("location:blog.php?id=$idBlog");
		exit();	
	}
}	
if ($fileName = $_FILES['file']['name'] != "") {
	$file = $_FILES['file'];
	$fileName = $_FILES['file']['name'];
	$fileTmpName = $_FILES['file']['tmp_name'];
	$fileSize = $_FILES['file']['size'];
	$fileError = $_FILES['file']['error'];
	$fileType = $_FILES['file']['type'];
	$fileExt = explode('.', $fileName);
	$fileActualExt = strtolower(end($fileExt));
	$allowed = array('jpg', 'jpeg','png', 'pdf');
	if (in_array($fileActualExt, $allowed)) {
		if ($fileError === 0) {
			if ($fileSize < 5000000) {
				$fileNameNew = uniqid('', true).".".$fileActualExt;
				$fileDestination = 'uploads/'.$fileNameNew;
				move_uploaded_file($fileTmpName, $fileDestination);
				$queryInsTit = "INSERT INTO multimedia(file) VALUES('$fileNameNew')";
				$resInsTit = $mysqli -> query($queryInsTit);
				$querySelctFotoId = "SELECT idmultimedia from multimedia where file = '$fileNameNew'";
				$resSelectFotoId = $mysqli -> query($querySelctFotoId);
				$rowSelectFotoId = $resSelectFotoId -> fetch_assoc();
				$idFoto = $rowSelectFotoId['idmultimedia'];
				$queryInsertFoto = "INSERT INTO foto(idpost, idfoto) VALUES('$idPostFoto','$idFoto')";
				$resInsertFoto = $mysqli -> query($queryInsertFoto);
				//se il file non è caricato correttamente mostro i messaggi di errore
			} else {
				$_SESSION["erroreCaricamentoImmagine"] = "Il tuo file è troppo grande";
			}
		} else {
			$_SESSION["erroreCaricamentoImmagine"] = "Errore nel caricamento";
		}
		
	} else {
		$_SESSION["erroreCaricamentoImmagine"] = "Non puoi caricare file di questo tipo";
	}
}
header("location:blog.php?id=$idBlog");	
?>


