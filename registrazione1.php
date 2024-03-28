<?php
session_start();
//mi connetto al database
include('connetti.php');
//estraggo i dati dal post
extract($_POST);	
$_SESSION["nome"] = $_POST["nome"];
$_SESSION["cognome"] = $_POST["cognome"];
$_SESSION["nickname"] = $_POST["nickname"];
$_SESSION["email"] = $_POST["email"];
$_SESSION["documento"] = $_POST["documento"];
$_SESSION["telefono"] = $_POST["telefono"];
$_SESSION["Bio"] = $_POST["Bio"];
$_SESSION["carta"] = $_POST["carta"];
$_SESSION["CVC"] = $_POST["CVC"];
//se i campi del form sono compilati correttamente registro l'utente
if (isset($_POST["nome"], $_POST["cognome"], $_POST["nickname"], $_POST["email"], $_POST["documento"], $_POST["telefono"], $_POST["Bio"], $_POST["password"]) && (preg_match('/^([A-Za-z\'àèìòù]+\s*)+$/i', $_POST["nome"])) && (preg_match('/^([A-Za-z\'àèìòù]+\s*)+$/i', $_POST["cognome"])) && (preg_match('/^\w+$/i', $_POST["nickname"])) && (filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) && (preg_match('/^\w+$/i', $_POST["documento"])) && (preg_match('/^[0-9]{10}$/i', $_POST["telefono"])) && (preg_match('/^\w+$/i', $_POST["password"]))) {
	$documento = htmlspecialchars($_POST['documento']);
	$nome = htmlspecialchars($_POST['nome']);
	$cognome = htmlspecialchars($_POST['cognome']);
	$email = htmlspecialchars($_POST['email']);
	$telefono = htmlspecialchars($_POST['telefono']);
	$bio = htmlspecialchars($_POST['Bio']);
	$password = htmlspecialchars($_POST['password']);	
	$nickname = htmlspecialchars($_POST['nickname']);
	$upgrade = '0';
	$tipo = "standard";
	//aggiungo l'utente al database
	$query = $mysqli -> prepare("INSERT INTO utente(nickname, documento, nome, cognome, telefono, mail, password, bio, upgrade, tipo) VALUES(?,?,?,?,?,?,?,?,?,?)");
	$query -> bind_param('ssssisssis', $nickname, $documento, $nome, $cognome, $telefono, $email, $password, $bio, $upgrade, $tipo);
	//creo la sessione con l'id dell'utente che si è appena registrato
	$sql = $mysqli -> prepare("SELECT idutente, nickname FROM utente WHERE nickname = ?");
	$sql -> bind_param('s', $nickname);
	//controllo se il nome, documento, email esistono
	$queryCheckDati = $mysqli -> prepare("SELECT * FROM utente WHERE nickname = ? OR documento = ? OR mail = ?");
	$queryCheckDati -> bind_param('sss', $nickname, $documento, $email);
	$queryCheckDati -> execute();
	$resCheckDati = $queryCheckDati -> get_result();
	//se il nickname o il documento o la mail esistono mostro il messaggio di errore
	if ($resCheckDati -> num_rows != 0) {
		$_SESSION["ErrRegistrazione"] = "Errore di registrazione";
		header('location: login.php');
	} else {
		//utente si iscrive senza carta o immagine di profilo
		if ((!isset($_POST["carta"]) || $_POST["carta"] == "") && $_FILES['file']['name'] == "") {
			//eseguo le query
			$query -> execute();
			$sql -> execute();
			$resultSql = $sql -> get_result();
			$rowSql = $resultSql -> fetch_assoc();
			$_SESSION["username"] = $rowSql["nickname"];
			$_SESSION["idutente"] = $rowSql["idutente"];
			$id = $_SESSION["idutente"];
			header('location: home2.php');
		}
		//utente si iscrive con carta ma senza immagine di profilo
		if(($_POST["carta"] != "" && preg_match('/^[0-9]{16}$/i', $_POST["carta"]) && $_POST["CVC"] != "" && preg_match('/^[0-9]{3}$/i', $_POST["CVC"]))) {
			if ($_FILES['file']['name'] == "") {
				$carta = $_POST["carta"];
				//eseguo le query
				$query -> execute();
				$sql -> execute();
				$resultSql = $sql -> get_result();
				$rowSql = $resultSql -> fetch_assoc();
				$_SESSION["username"] = $rowSql["nickname"];
				$_SESSION["idutente"] = $rowSql["idutente"];
				$id = $_SESSION["idutente"];
				$tipo = "premium";
				//aggiorno i dati della carta dell'utente e il tipo di utente
				$queryAggiungiCarta = $mysqli -> prepare("UPDATE utente SET numerocarta = ?, tipo = '$tipo' WHERE idutente = '$id'");
				$queryAggiungiCarta -> bind_param('s', $carta);
				//eseguo la query
				$queryAggiungiCarta -> execute();
				header('location: home2.php');
			}
		} else {
			//se i campi relativi alla carta di credito non sono compilati correttamente mostro il messaggio di errore
			if ($_POST["carta"] != "") {
				$_SESSION["ErroreCarta"] = "Errore nell'inserimento dei dati della carta di credito";
				header('location: login.php');
			}
		}
		//utente si iscrive con carta e con immagine di profilo
		if(($_POST["carta"] != "" && preg_match('/^[0-9]{16}$/i', $_POST["carta"]) && $_POST["CVC"] != "" && preg_match('/^[0-9]{3}$/i', $_POST["CVC"])) && $_FILES['file']['name'] != ""){
			//prendo il file
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
					if ($fileSize < 500000) {
						$carta = $_POST["carta"];
						//se il file è corretto eseguo le query
						$query -> execute();
						$sql -> execute();
						$resultSql = $sql -> get_result();
						$rowSql = $resultSql -> fetch_assoc();
						$_SESSION["username"] = $rowSql["nickname"];
						$_SESSION["idutente"] = $rowSql["idutente"];
						$id = $_SESSION["idutente"];
						$tipo = "premium";
						//query per aggiornare i dati della carta di credito dell'utente e del tipo dell'utente
						$queryAggiungiCarta = $mysqli -> prepare("UPDATE utente SET numerocarta = ?, tipo = '$tipo' WHERE idutente = '$id'");
						$queryAggiungiCarta -> bind_param('s', $carta);
						$queryAggiungiCarta -> execute();
						//carico l'immagine di profilo
						$fileNameNew = uniqid('', true).".".$fileActualExt;
						$fileDestination = 'uploads/'.$fileNameNew;				
						move_uploaded_file($fileTmpName, $fileDestination);
						//inserisco la foto nel database
						$queryInsTit = "INSERT INTO multimedia(file) VALUES('$fileNameNew')";
						$resInsTit = $mysqli -> query($queryInsTit);
						//prendo l'id della foto
						$querySelctFotoId = "SELECT idmultimedia from multimedia where file = '$fileNameNew'";
						$resSelectFotoId = $mysqli -> query($querySelctFotoId);
						$rowSelectFotoId = $resSelectFotoId -> fetch_assoc();
						$idFoto = $rowSelectFotoId['idmultimedia'];
						//metto la foto al profilo
						$queryInsertFoto = "INSERT INTO FotoProfilo(idUtente, idFoto) VALUES('$id','$idFoto')";
						$resInsertFoto = $mysqli -> query($queryInsertFoto);
						header('location: home2.php');
					} else {
						//se il file è troppo grande mostro il messaggio di errore
						$_SESSION["erroreUpload"] = "Il file inserito è troppo grande";
						header('location: login.php');
					}
				} else {
					//se il file non è caricato correttamente mostro il messaggio di errore
					$_SESSION["erroreUpload"] = "Errore nel caricamento del file";
					header('location: login.php');
				}
			} else {
				//se il file non è del tipo ammesso mostro il messaggio di errore
				$_SESSION["erroreUpload"] = "Non puoi caricare file di questo tipo";
				header('location: login.php');
			}
		} else {
			//se i campi della carat non sono compilati correttamente mostro il messaggio di errore
			if (isset($_SESSION["erroreUpload"])) {
				$_SESSION["ErroreCarta"] = "Errore nell'inserimento dei dati della carta di credito";
				header('location: login.php');
			}
		}
		//utente inserisce un'immagine di profilo ma non la carta
		if (($_POST["carta"] == "") &&  $_FILES['file']['name'] != "") {
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
					if ($fileSize < 500000) {
						$carta = $_POST["carta"];
						//eseguo le query
						$query -> execute();
						$sql -> execute();
						$resultSql = $sql -> get_result();
						$rowSql = $resultSql -> fetch_assoc();
						$_SESSION["username"] = $rowSql["nickname"];
						$_SESSION["idutente"] = $rowSql["idutente"];
						$id = $_SESSION["idutente"];
						//carico l'immagine di profilo
						$fileNameNew = uniqid('', true).".".$fileActualExt;
						$fileDestination = 'uploads/'.$fileNameNew;				
						move_uploaded_file($fileTmpName, $fileDestination);
						//inserisco la foto nel database
						$queryInsTit = "INSERT INTO multimedia(file) VALUES('$fileNameNew')";
						$resInsTit = $mysqli -> query($queryInsTit);
						//prendo l'id della foto
						$querySelctFotoId = "SELECT idmultimedia from multimedia where file = '$fileNameNew'";
						$resSelectFotoId = $mysqli -> query($querySelctFotoId);
						$rowSelectFotoId = $resSelectFotoId -> fetch_assoc();
						$idFoto = $rowSelectFotoId['idmultimedia'];
						//metto la foto al profilo
						$queryInsertFoto = "INSERT INTO FotoProfilo(idUtente, idFoto) VALUES('$id','$idFoto')";
						$resInsertFoto = $mysqli -> query($queryInsertFoto);
						header('location: home2.php');
					} else {
						//se il file è troppo grande mostro il messaggio di errore
						$_SESSION["erroreUpload"] = "Il file inserito è troppo grande";
						header('location:login.php');
					}
				} else {
					//se il file non è caricato correttamente mostro il messaggio di errore
					$_SESSION["erroreUpload"] = "Errore nel caricamento del file";
					header('location:login.php');
				}
			} else {
				//se il file non è del tipo ammesso mostro il messaggio di errore
				$_SESSION["erroreUpload"] = "Non puoi caricare file di questo tipo";
				header('location:login.php');
			}
		}
	}
} else {
	//se il nome non è inserito correttamente mostro il messaggio di errore
	if (!isset($_POST["nome"]) || (!preg_match('/^([A-Za-z\'àèìòù]+\s*)+$/i', $_POST["nome"]))) {
		$_SESSION["ErroreNome"] = "Nome non inserito correttamente";
		header('location: login.php');
	}
	//se il cognome non è inserito correttamente mostro il messaggio di errore
	if (!isset($_POST["cognome"]) || (!preg_match('/^([A-Za-z\'àèìòù]+\s*)+$/i', $_POST["cognome"]))) {
		$_SESSION["ErroreCognome"] = "Cognome non inserito correttamente";
		header('location: login.php');
	}
	//se il nickname non è inserito correttamente mostro il messaggio di errore
	if (!isset($_POST["nickname"]) || (!preg_match('/^\w+$/i', $_POST["nickname"]))) {
		$_SESSION["ErroreNickname"] = "Nickname non inserito correttamente";
		header('location: login.php');
	}
	//se la mail non è inserita correttamente mostro il messaggio di errore
	if (!isset($_POST["email"]) || (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL))) {
		$_SESSION["ErroreMail"] = "E-mail non inserita correttamente";
		header('location: login.php');
	}
	//se il documento non è inserito correttamente mostro il messaggio di errore
	if (!isset($_POST["documento"]) || (!preg_match('/^\w+$/i', $_POST["documento"]))) {
		$_SESSION["ErroreDocumento"] = "Documento non inserito correttamente";
		header('location: login.php');
	}
	//se il telefono non è inserito correttamente mostro il messaggio di errore
	if (!isset($_POST["telefono"]) || (!preg_match('/^[0-9]{10}$/i', $_POST["telefono"]))) {
		$_SESSION["ErroreTelefono"] = "Numero di telefono non inserito correttamente";
		header('location: login.php');
	}
	//se la password non è inserita correttamente mostro il messaggio di errore
	if (!isset($_POST["password"]) || (!preg_match('/^\w+$/i', $_POST["password"]))) {
		$_SESSION["ErrorePassword"] = "Password non inserita correttamente";
		header('location: login.php');
	}
}
?>