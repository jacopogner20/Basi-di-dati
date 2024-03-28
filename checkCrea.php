<?php
session_start();
//mi connetto al database
include('connetti.php');
//estraggo i dati dal POST
extract($_POST);
$nome = $_SESSION["username"];
$idUtente = $_SESSION["idutente"];
//se il file non è del tipo giusto mostro l'errore e rimando alla pagina di creazione del blog
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
		$_SESSION["msgCrea"] = "Non puoi caricare questo tipo di file";
		header("location:crea.php");
		exit();	
	}
}
//se il file non è del tipo giusto mostro l'errore e rimando alla pagina di creazione del blog	
if ($fileNameSfondo = $_FILES['fileSfondo']["name"] != "") {
	$fileNameSfondo = $_FILES['fileSfondo']['name'];
	$fileTmpNameSfondo = $_FILES['fileSfondo']['tmp_name'];
	$fileSizeSfondo = $_FILES['fileSfondo']['size'];
	$fileErrorSfondo = $_FILES['fileSfondo']['error'];
	$fileTypeSfondo = $_FILES['fileSfondo']['type'];
	$fileExtSfondo = explode('.', $fileNameSfondo);
	$fileActualExtSfondo = strtolower(end($fileExtSfondo));
	$allowed = array('jpg', 'jpeg','png', 'pdf');
	if (!in_array($fileActualExtSfondo, $allowed)) {
		$_SESSION["msgCrea"] = "Non puoi caricare questo tipo di file";
		header("location:crea.php");
		exit();	
	}
}
//se il nome del blog e del tema sono corretti creo il blog
if($nomeBlog != "" && $temaBlog != "" && (preg_match('/^([A-Za-z0-9\'àèìòù:!?,]+\s*)+$/i', $_POST["nomeBlog"])) && (preg_match('/^(\w*\s*\'{0,1}\s*\w*)*$/i', $_POST["temaBlog"]))) {
	if(isset($_POST["font"])){
		$font = $_POST["font"];
	}
	$nomeBlog = htmlspecialchars($_POST['nomeBlog']);
	$temaBlog = htmlspecialchars($_POST['temaBlog']);
	//controllo se il blog esiste
	$queryEsistenzaBlog = $mysqli -> prepare("SELECT idblog FROM blog WHERE titolo = ?");
	$queryEsistenzaBlog -> bind_param('s', $nomeBlog);
	$queryEsistenzaBlog -> execute();
	$resEsistenzaBlog = $queryEsistenzaBlog -> get_result();
	//se il blog non esiste
	if ($resEsistenzaBlog -> num_rows == 0) {
		//inserisco il blog nel database
		$query = $mysqli -> prepare("INSERT INTO blog(titolo, autore, font) VALUES(?,?, ?)");
		$query -> bind_param('ssi', $nomeBlog, $idUtente, $font);
		$query -> execute();
		$risultato = $query -> get_result();
		//controllo se il tema esiste
		$queryTema = $mysqli -> prepare("SELECT nometema FROM tema WHERE nometema = ?");
		$queryTema -> bind_param('s', $temaBlog);
		$queryTema -> execute();
		$resNomeTema = $queryTema -> get_result();
		//se non esiste lo inserisco nel database
		if($resNomeTema -> num_rows == 0){
			//inserisco il tema nel database
			$queryInsertTema = $mysqli -> prepare("INSERT INTO tema(nometema) VALUES(?)");
			$queryInsertTema -> bind_param('s', $temaBlog);
			$queryInsertTema -> execute();
			$resInsertTema = $queryInsertTema -> get_result();
		}
		//prendo l'id del tema
		$queryIDTema = $mysqli -> prepare("SELECT idtema FROM tema WHERE nometema = ?");
		$queryIDTema -> bind_param('s', $temaBlog);
		$queryIDTema -> execute();
		$risultatoIDTema = $queryIDTema -> get_result();
		$rowIDTema = $risultatoIDTema->fetch_assoc();
		$idTema = $rowIDTema["idtema"];
		//prendo l'id del blog
		$queryIDBlog = $mysqli -> prepare("SELECT idblog FROM blog WHERE titolo = ?");
		$queryIDBlog -> bind_param('s', $nomeBlog);
		$queryIDBlog -> execute();
		$risultatoIDBlog = $queryIDBlog -> get_result();
		$rowIDBlog = $risultatoIDBlog->fetch_assoc();
		$idBlog = $rowIDBlog["idblog"];
		//carico l'immagine del blog
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
					//inserisco l'immagine di profilo del blog
					$queryInsertFoto = "INSERT INTO FotoBlog(idBlog, idFoto, sfondo) VALUES('$idBlog','$idFoto', '0')";
					$resInsertFoto = $mysqli -> query($queryInsertFoto);
				//messaggi di errore se il file non è corretto
				} else {
					$_SESSION["msgCrea"] = "Errore nel caricamento del file";
				}
			} else {
				$_SESSION["msgCrea"] = "Errore nel caricamento del file";
			}	
		} else {
			if ($fileName == "") {
				header("location: blog.php?id=".$idBlog."");
			} else {
				$_SESSION["msgCrea"] = "Errore nel caricamento del file";
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
					if ($resSelectControllo -> num_rows == 0) {
						$queryInsTit = "INSERT INTO multimedia(file) VALUES('$fileNameNewSfondo')";
						$resInsTit = $mysqli -> query($queryInsTit);
						$queySelctID = "SELECT idmultimedia from  multimedia where file = '$fileNameNewSfondo'";
						$resSelectID = $mysqli -> query($queySelctID);
						$rowSelectID = $resSelectID -> fetch_assoc();
						$idFotoDaPrendere =$rowSelectID['idmultimedia'];
						$idFotoNuova = $rowSelectID['idmultimedia'];
						$queryInsertFotoNuova = "INSERT INTO FotoBlog(idFoto, idBlog, sfondo) VALUES ('$idFotoNuova', '$idBlog', '1')";
						$resInsertFotoNuova = $mysqli -> query($queryInsertFotoNuova);		
					} 
				//se il file non è caricato correttamente mostro l'errore
				} else {
					$_SESSION["msgCrea"] = "Immagine di sfondo non inserita correttamente";
					header("location: crea.php");	
				}
			} else {
				$_SESSION["msgCrea"] = "Immagine di sfondo non inserita correttamente";
				header("location: crea.php");
			}	
		} else {
			if($fileNameSfondo == ""){
				header("location: crea.php");
			} else {
				$_SESSION["msgCrea"] = "Immagine di sfondo non inserita correttamente";
				header("location: crea.php");
			}
		}
		//inserisco il blog e il tema nella tabella "tematica"
		$queryInsertTematica = "INSERT INTO tematica(idblog, idtema) VALUES('$idBlog', '$idTema')";
		$resInsertTematica = $mysqli -> query($queryInsertTematica);
		if ($_SESSION["msgCrea"] == "") {
			header("location: blog.php?id=".$idBlog.""); 		
		}
	//se esiste un altro blog con quel titolo
	} else {
		header('location: crea.php');
		$_SESSION["msgCrea"] = "Nome blog già esistente";
	}
} else {
	//se i campi non sono compilati correttamente mostro l'errore
	header('location: crea.php');
	$_SESSION["msgCrea"] = "I campi non sono stati compilati correttamente";
}
?>