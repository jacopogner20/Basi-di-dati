<?php
session_start();
$idutente = $_SESSION["idutente"];
//echo $idutente;
if(isset($_GET["idBlog"])){
	$idBlog = $_GET["idBlog"];
}
//mi connetto al database
include('connetti.php');
//estraggo i dati dal post
extract($_POST);
if (!isset($_GET["idBlog"])) {
	//se i dati dell'utente sono correttamente inseriti
	if (isset($_POST["nome"], $_POST["cognome"], $_POST["nickname"], $_POST["email"], $_POST["documento"], $_POST["telefono"], $_POST["Bio"], $_POST["password"]) && (preg_match('/^([A-Za-z\'àèìòù]+\s*)+$/i', $_POST["nome"])) && (preg_match('/^([A-Za-z\'àèìòù]+\s*)+$/i', $_POST["cognome"])) && (preg_match('/^\w+$/i', $_POST["nickname"])) && (filter_var($_POST["email"],FILTER_VALIDATE_EMAIL)) && (preg_match('/^\w+$/i', $_POST["documento"])) && (preg_match('/^[0-9]{10}$/i', $_POST["telefono"])) && (preg_match('/^\w+$/i', $_POST["password"]))) {
		$nome = $_POST["nome"];
		$cognome = $_POST["cognome"];
		$nickname = $_POST["nickname"];
		$mail = $_POST["email"];
		$documento = $_POST["documento"];
		$telefono = $_POST["telefono"];
		$bio = htmlspecialchars($_POST["Bio"]);
		$password = $_POST["password"];
		$_SESSION["username"] = $nickname;
		//preparo la query di aggiornamento dati dell'utente
		$sql = $mysqli -> prepare("UPDATE utente SET nome = ?, cognome = ?, mail = ?, nickname = ?, documento = ?, telefono = ?, bio = ?, password = ? WHERE idutente = '$idutente'");
		$sql -> bind_param('sssssiss', $nome, $cognome, $mail, $nickname, $documento, $telefono, $bio, $password);
		$sql -> execute();
		//invio la query
		$result = $sql -> get_result();
		$errModifica = $sql -> error;
		//controllo l'esito
		if($errModifica != ""){
			$_SESSION["messaggio"] = "Modifiche non apportate correttamente";
		}
		//query che rende premium l'utente
		if(isset($_POST['carta']) && $_POST['carta'] != "" && preg_match('/^\d{16}$/i', $_POST['carta'])){
			$carta = trim($_POST['carta']);
			$tipo = "premium";
			$queryAggiungiCarta = $mysqli -> prepare("UPDATE utente SET numerocarta = ?, tipo = '$tipo' WHERE idutente = '$idutente'");
			$queryAggiungiCarta -> bind_param('s', $carta);
			$queryAggiungiCarta -> execute();
			$resAggiungiCarta = $queryAggiungiCarta -> get_result(); 
		} else {
			if ($_POST["carta"] == "" && $_POST["CVV"] == "") {
				header('location: profilo.php?id=$idutente');
			} else {
				//se i dati della carta non sono inseriti correttamente mostro il messaggio di errore
				header('location: profilo.php?id=$idutente');
				$_SESSION["messaggio"] = "Modifiche non apportate correttamente";
			}
		}
		//aggiorno l'immagine profilo dell'utente
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
					$fileNameNew = uniqid('', true).".".$fileActualExt;
					$fileDestination = 'uploads/'.$fileNameNew;
					move_uploaded_file($fileTmpName, $fileDestination);
					//prendo l'id del file
					$querySelectControllo = "SELECT idmultimedia from  multimedia where file = '$fileNameNew'";
					$resSelectControllo= $mysqli -> query($querySelectControllo);
					$rowSelectControllo = $resSelectControllo -> fetch_assoc();	
					//se il file non esiste lo aggiungo alla tabella multimedia				
					if ($resSelectControllo -> num_rows == 0) {
						$queryInsTit = "INSERT INTO multimedia(file) VALUES('$fileNameNew')";
						$resInsTit = $mysqli -> query($queryInsTit);
						//prendo l'id del file appena aggiunto
						$queySelctID = "SELECT idmultimedia from  multimedia where file = '$fileNameNew'";
						$resSelectID = $mysqli -> query($queySelctID);
						$rowSelectID = $resSelectID -> fetch_assoc();
						$idFotoDaPrendere =$rowSelectID['idmultimedia'];
						//guardo se l'utente ha già un'immagine di profilo
						$queySelctFoto = "SELECT idFoto from  FotoProfilo where idUtente = '$idutente'";
						$resSelectFoto = $mysqli -> query($queySelctFoto);
						$rowSelectFoto = $resSelectFoto -> fetch_assoc();
						//se l'utente ha già un'immagine di profilo la aggiorno
						if ($resSelectFoto -> num_rows > 0) {
							$queryUpdateImmagine = "UPDATE FotoProfilo SET  idFoto = '$idFotoDaPrendere' where idUtente = '$idutente'";
							$resUpdateImmagine = $mysqli -> query($queryUpdateImmagine);	
						} else {
							//se l'utente non ha un'immagine di profilo la aggiungo
							$idFotoNuova = $rowSelectID['idmultimedia'];
							$queryInsertFotoNuova = "INSERT INTO FotoProfilo(idFoto, idUtente) VALUES ('$idFotoNuova', '$idutente')";
							$resInsertFotoNuova = $mysqli -> query($queryInsertFotoNuova);		
						}
					} else {
						//se l'immagine di profilo esiste già nel database la aggiorno
						$idFoto = $rowSelectControllo['idmultimedia'];
						$queryAggiornaFotoNuova = "UPDATE FotoProfilo SET  idFoto = '$idFoto' where idUtente = '$idutente'";
						$resAggiornaFotoNuova = $mysqli -> query($queryAggiornaFotoNuova);
					}
				//se il file non è inserito correttamente mostro i messaggi di errore
				} else {
					$_SESSION["messaggio"] = "Modifiche non apportate correttamente";
				}
			} else {
				$_SESSION["messaggio"] = "Modifiche non apportate correttamente";
			}	
		} else {
			if($_FILES['file']['name'] == ""){
				header('location: profilo.php?id=$idutente');
			} else {
				$_SESSION["messaggio"] = "Modifiche non apportate correttamente";
			}
		}
		header("location: profilo.php?id=$idutente");
	} else {
		//se il nome non è inserito correttamente mostro il messaggio di errore
		if (isset($_POST["nome"]) && (!preg_match('/^([A-Za-z\'àèìòù]+\s*)+$/i', $_POST["nome"]))) {
			$_SESSION["messaggio"] = "Nome inserito non valido";
			header("location: profilo.php?id=$idutente");
		}
		//se il cognome non è inserito correttamente mostro il messaggio di errore
		if (isset($_POST["cognome"]) && (!preg_match('/^([A-Za-z\'àèìòù]+\s*)+$/i', $_POST["cognome"]))) {
			$_SESSION["messaggio"] = "Cognome inserito non valido";
			header("location: profilo.php?id=$idutente");
		}
		//se il nickname non è inserito correttamente mostro il messaggio di errore
		if (isset($_POST["nickname"]) && (!preg_match('/^\w+$/i', $_POST["nickname"]))) {
			$_SESSION["messaggio"] = "Nickname inserito non valido";
			header("location: profilo.php?id=$idutente");
		}
		//se la mail non è inserita correttamente mostro il messaggio di errore
		if (isset($_POST["email"]) && (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL))) {
			$_SESSION["messaggio"] = "E-mail inserita non valida";
			header("location: profilo.php?id=$idutente");
		}
		//se il documento non è inserito correttamente mostro il messaggio di errore
		if (isset($_POST["documento"]) && (!preg_match('/^\w+$/i', $_POST["documento"]))) {
			$_SESSION["messaggio"] = "Documento inserito non valido";
			header("location: profilo.php?id=$idutente");
		}
		//se il telefono non è inserito correttamente mostro il messaggio di errore
		if (isset($_POST["telefono"]) && (!preg_match('/^[0-9]{10}$/i', $_POST["telefono"]))) {
			$_SESSION["messaggio"] = "Telefono inserito non valido";
			header("location: profilo.php?id=$idutente");
		}
		//se la password non è inserita correttamente mostro il messaggio di errore
		if (isset($_POST["password"]) && !preg_match('/^\w+$/i', $_POST["password"])) {
			$_SESSION["messaggio"] = "Password inserita non valida";
			header("location: profilo.php?id=$idutente");
		}
	}
} else {
	//se il titolo del blog è inserito correttamente aggiorno i dati del blog
	if(isset($_POST["titolo"]) && (preg_match('/^([A-Za-z0-9\'àèìòù:!?,]+\s*)+$/i', $_POST["titolo"]))) {
		if (isset($_POST["font"])) {
			$font = $_POST["font"];
		}
		$titolo = $_POST["titolo"];
		//preparo la query di aggiornamento dati del blog
		$sql = $mysqli -> prepare("UPDATE blog SET titolo = ?, font = ? WHERE idblog = '$idBlog'");
		$sql -> bind_param('si', $titolo, $font);
		$sql -> execute();
		$res = $sql -> get_result();
		$errBlog = $sql -> error;
		//se i dati del blog non sono aggiornati correttamente mostro il messaggio di errore
		if($errBlog != ""){
			$_SESSION["messaggioBlog"] = "Modifiche non apportate correttamente";
			header("location: blog.php?id=$idBlog");
		} 
		//se il nickname del coautore è inserito correttamente:
		if (isset($_POST["coautore"]) && $_POST["coautore"] != "" && (preg_match('/^\w+$/i', $_POST["coautore"]))) {
			$coautore = $_POST["coautore"];
			//prendo l'id del coautore
			$query = $mysqli -> prepare("SELECT idutente FROM utente WHERE nickname = ?");
			$query -> bind_param('s', $coautore);
			$query -> execute();
			$resIdCoautore = $query -> get_result();
			$rowIDCoautore = $resIdCoautore -> fetch_assoc();
			$idCoautore = $rowIDCoautore["idutente"];
			//se il coautore è diverso dall'utente loggato e se esiste lo aggiungo
			if(($idutente != $idCoautore) && $resIdCoautore -> num_rows != 0){
				//preparo la query coautore
				$sqlCoautore = "INSERT INTO coautore(idcoautore, idblog) VALUES ('$idCoautore','$idBlog')";
				$resSqlCoautore = $mysqli -> query($sqlCoautore);
				//se il coautore non è inserito correttamente mostro il messaggio di errore
				if(!$resSqlCoautore){
					$_SESSION["messaggioBlog"] = "Coautore non inserito correttamente";
				}
			//se il nickname non esiste mostro il messaggio di errore
			} else {
				$_SESSION["messaggioBlog"] = "Coautore non inserito correttamente";
			}
		}
		//carico la foto di profilo del blog
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
					//prendo l'id della foto caricata
					$querySelectControllo = "SELECT idmultimedia from  multimedia where file = '$fileNameNew'";
					$resSelectControllo= $mysqli -> query($querySelectControllo);
					$rowSelectControllo = $resSelectControllo -> fetch_assoc();	
					//se la foto non è presente nel database la aggiungo				
					if ($resSelectControllo -> num_rows == 0) {
						$queryInsTit = "INSERT INTO multimedia(file) VALUES('$fileNameNew')";
						$resInsTit = $mysqli -> query($queryInsTit);
						//prendo l'id della foto appena aggiunta
						$queySelctID = "SELECT idmultimedia from  multimedia where file = '$fileNameNew'";
						$resSelectID = $mysqli -> query($queySelctID);
						$rowSelectID = $resSelectID -> fetch_assoc();
						$idFotoDaPrendere =$rowSelectID['idmultimedia'];
						//guardo se il blog ha già un'immagine di profilo
						$queySelctFoto = "SELECT idFoto from  FotoBlog where idBlog = '$idBlog' AND sfondo = '0'";
						$resSelectFoto = $mysqli -> query($queySelctFoto);
						$rowSelectFoto = $resSelectFoto -> fetch_assoc();
						//se il blog ha già un'immagine di profilo la aggiorno
						if ($resSelectFoto -> num_rows > 0) {
							$queryUpdateImmagine = "UPDATE FotoBlog SET  idFoto = '$idFotoDaPrendere' where idBlog = '$idBlog' AND sfondo = '0'";
							$resUpdateImmagine = $mysqli -> query($queryUpdateImmagine);	
						} else {
							//se il blog non ha già un'immagine di profilo la aggiungo
							$idFotoNuova = $rowSelectID['idmultimedia'];
							$queryInsertFotoNuova = "INSERT INTO FotoBlog(idFoto, idBlog, sfondo) VALUES ('$idFotoNuova', '$idBlog', '0')";
							$resInsertFotoNuova = $mysqli -> query($queryInsertFotoNuova);		
						}
					} else {
						//se la foto è già presente nel database la aggiorno
						$idFoto = $rowSelectControllo['idmultimedia'];
						$queryAggiornaFotoNuova = "UPDATE FotoBlog SET  idFoto = '$idFoto' where idBlog = '$idBlog' AND sfondo = '0'";
						$resAggiornaFotoNuova = $mysqli -> query($queryAggiornaFotoNuova);
					}
				//se il file non è inserito correttamente mostro i messaggi di errore
				} else {
					$_SESSION["messaggioBlog"] = "Modifiche non apportate correttamente";
					header("location: blog.php?id=$idBlog");
				}
			} else {
				$_SESSION["messaggioBlog"] = "Modifiche non apportate correttamente";
				header("location: blog.php?id=$idBlog");
			}	
		} else {
			if ($fileName == "") {
				header("location: blog.php?id=$idBlog");
			} else {
				$_SESSION["messaggioBlog"] = "Modifiche non apportate correttamente";
				header("location: blog.php?id=$idBlog");
			}
		}
		//carico la foto di sfondo
		$fileSfondo = $_FILES['fileSfondo'];
		$fileNameSfondo = $_FILES['fileSfondo']['name'];
		$fileTmpNameSfondo = $_FILES['fileSfondo']['tmp_name'];
		$fileSizeSfondo = $_FILES['fileSfondo']['size'];
		$fileErrorSfondo = $_FILES['fileSfondo']['error'];
		$fileTypeSfondo = $_FILES['fileSfondo']['type'];
		$fileExtSfondo = explode('.', $fileNameSfondo);
		$fileActualExtSfondo = strtolower(end($fileExtSfondo));
		$allowed = array('jpg', 'jpeg','png', 'pdf');
		if (in_array($fileActualExtSfondo, $allowed)) {
			if ($fileErrorSfondo === 0) {
				if ($fileSizeSfondo < 5000000) {
					$fileNameNewSfondo = uniqid('', true).".".$fileActualExtSfondo;
					$fileDestination = 'uploads/'.$fileNameNewSfondo;
					move_uploaded_file($fileTmpNameSfondo, $fileDestination);
					//controllo se la foto è nel database
					$querySelectControllo = "SELECT idmultimedia from  multimedia where file = '$fileNameNewSfondo'";
					$resSelectControllo= $mysqli -> query($querySelectControllo);
					$rowSelectControllo = $resSelectControllo -> fetch_assoc();	
					//se la foto di sfondo non è nel database la aggiungo				
					if ($resSelectControllo -> num_rows == 0) {
						$queryInsTit = "INSERT INTO multimedia(file) VALUES('$fileNameNewSfondo')";
						$resInsTit = $mysqli -> query($queryInsTit);
						//prendo l'id della foto appena aggiunta
						$queySelctID = "SELECT idmultimedia from  multimedia where file = '$fileNameNewSfondo'";
						$resSelectID = $mysqli -> query($queySelctID);
						$rowSelectID = $resSelectID -> fetch_assoc();
						$idFotoDaPrendere =$rowSelectID['idmultimedia'];
						//guardo se il blog ha già un'immagine di sfondo
						$queySelctFoto = "SELECT idFoto from  FotoBlog where idBlog = '$idBlog' AND sfondo = 1";
						$resSelectFoto = $mysqli -> query($queySelctFoto);
						$rowSelectFoto = $resSelectFoto -> fetch_assoc();
						//se il blog ha già un'immagine di sfondo la aggiorno
						if ($resSelectFoto -> num_rows > 0) {
							$queryUpdateImmagine = "UPDATE FotoBlog SET  idFoto = '$idFotoDaPrendere' where idBlog = '$idBlog' AND sfondo = 1";
							$resUpdateImmagine = $mysqli -> query($queryUpdateImmagine);
						//se il blog non ha un'immagine di sfondo la aggiungo	
						} else {
							$idFotoNuova = $rowSelectID['idmultimedia'];
							$queryInsertFotoNuova = "INSERT INTO FotoBlog(idFoto, idBlog, sfondo) VALUES ('$idFotoNuova', '$idBlog', '1')";
							$resInsertFotoNuova = $mysqli -> query($queryInsertFotoNuova);
							if (!$resInsertFotoNuova) {
								echo $mysqli -> error;
							}		
						}
					//se la foto di sfondo esiste nel database la aggiorno
					} else {
						$idFoto = $rowSelectControllo['idmultimedia'];
						$queryAggiornaFotoNuova = "UPDATE FotoBlog SET  idFoto = '$idFoto' where idBlog = '$idBlog' AND sfondo = '1'";
						$resAggiornaFotoNuova = $mysqli -> query($queryAggiornaFotoNuova);
					}
				//se il file non è caricato correttamente mostro i messaggi di errore
				} else {
					$_SESSION["messaggioBlog"] = "Modifiche non apportate correttamente";
					header("location: blog.php?id=$idBlog");
				}
			} else {
				$_SESSION["messaggioBlog"] = "Modifiche non apportate correttamente";
				header("location: blog.php?id=$idBlog");
			}	
		} else {
			if($fileNameSfondo == ""){
				header("location: blog.php?id=$idBlog");
			} else {
				$_SESSION["messaggioBlog"] = "Modifiche non apportate correttamente";
				header("location: blog.php?id=$idBlog");
			}
		}
		//se il nickname del coautore da eliminare è inserito correttamente lo elimino
		if(isset($_POST["EliminaCoautore"]) && $_POST["EliminaCoautore"] != "" && (preg_match('/^\w+$/i', $_POST["EliminaCoautore"]))) {
			$eliminaC = $_POST["EliminaCoautore"];
			//prendo l'id dell'utente di cui ho inserito il nickname
			$queryIDCancella = $mysqli ->prepare("SELECT idutente FROM utente, coautore WHERE nickname = ? AND idutente = idcoautore AND idblog = '$idBlog'");
			$queryIDCancella -> bind_param('s', $eliminaC);
			$queryIDCancella -> execute();
			$resIDCancella = $queryIDCancella -> get_result();
			$rowIDCancella = $resIDCancella -> fetch_assoc();
			//se l'utente esiste lo elimino dalla tabella coautore
			if($resIDCancella -> num_rows != 0){
				$CancellaC = $rowIDCancella["idutente"];
				$queryElimina = "DELETE FROM coautore WHERE idcoautore = '$CancellaC' AND idblog = '$idBlog'";
				$resElimina = $mysqli -> query($queryElimina);
			//se l'utente non esiste o non è coautore mostro il messaggio di errore
			} else {
				$_SESSION["messaggioBlog"] = "Coautore non eliminato correttamente";
			}
		} 
		header("location: blog.php?id=$idBlog");
	//se i campi di modifica del blog non sono inseriti correttamente mostro il messaggio di errore
	} else {
		$_SESSION["messaggioBlog"] = "Modifiche non apportate correttamente";
		header("location: blog.php?id=$idBlog");
	}
}
//elimino il blog
if (isset($_POST["idBlogElimina"])) {
	$BlogElimina = $_POST["idBlogElimina"];
	$queryEliminaBlog = "DELETE FROM blog WHERE idblog = '$BlogElimina'";
	$resEliminaBlog = $mysqli -> query($queryEliminaBlog);
}
//elimino l'utente
if (isset($_POST["idUtenteElimina"])) {
	$UtenteElimina = $_POST["idUtenteElimina"];
	$queryEliminaUt = "DELETE FROM utente WHERE idutente = '$UtenteElimina'";
	$resEliminaUt = $mysqli -> query($queryEliminaUt);
}
//elimino il post
if(isset($_POST["idPostElimina"])){
	$EliminaP = $_POST["idPostElimina"];
	$queryEliminaPOST = "DELETE FROM post WHERE idpost = '$EliminaP'";
	$resEliminaPOST = $mysqli -> query($queryEliminaPOST);
}
//elimino il commento
if (isset($_POST["idCommentoElimina"])) {
	$EliminaC = $_POST["idCommentoElimina"];
	$queryEliminaCommento = "DELETE FROM commenti WHERE idcommento = '$EliminaC'";
	$resEliminaCommento = $mysqli -> query($queryEliminaCommento);
}
//elimino il tema
if (isset($_POST["idTemaElimina"])) {
	$EliminaTema = $_POST["idTemaElimina"];
	$queryEliminaTema = "DELETE FROM tema WHERE idtema = '$EliminaTema'";
	$resEliminaTema = $mysqli -> query($queryEliminaTema);
}
//annullo l'abbonamento 
if (isset($_POST["disdici"])) {
	$disdiciAbb = $_POST["disdici"];
	$queryAnnullaAbb = "UPDATE utente SET tipo = '$disdiciAbb' WHERE idutente = '$idutente'";
	$resAnnullaAbb = $mysqli -> query($queryAnnullaAbb);
}
?>