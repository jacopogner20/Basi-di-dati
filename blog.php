<?php
session_start();
if (isset($_SESSION["nome"], $_SESSION["cognome"],$_SESSION["nickname"],$_SESSION["documento"],$_SESSION["email"],$_SESSION["telefono"],$_SESSION["Bio"],$_SESSION["carta"],$_SESSION["CVC"])) {
	unset($_SESSION["nome"]);
	unset($_SESSION["cognome"]);
	unset($_SESSION["nickname"]);
	unset($_SESSION["documento"]);
	unset($_SESSION["email"]);
	unset($_SESSION["telefono"]);
	unset($_SESSION["Bio"]);
	unset($_SESSION["carta"]);
	unset($_SESSION["CVC"]);
}
//mi connetto al database
include('connetti.php');
if (isset($_GET["id"]) && preg_match('/^[0-9]+$/i', $_GET["id"])) {
	$idBlog = $_GET["id"];
	$_SESSION["idBlog"] = $idBlog;
}
if(isset($_SESSION["idutente"])) {
	$idutente = $_SESSION["idutente"];
	//query che controlla quanti utenti segue l'utente loggato e il tipo dell'utente loggato
	$queryFollowerTipo = "SELECT count(*) as Quanti, tipo FROM segui, utente where idfollower = idutente and idfollower = '$idutente'";
	$resQueryFollowerTipo = $mysqli -> query($queryFollowerTipo);
	$rowQueryFollowerTipo = $resQueryFollowerTipo -> fetch_assoc();
	//query che controlla quanti blog segue l'utente loggato e il tipo dell'utente loggato
	$queryBlogTipo = "SELECT count(*) as Quanti, tipo FROM segueblog, utente where segueblog.idutente = utente.idutente and utente.idutente= '$idutente'";
	$resQueryBlogTipo = $mysqli -> query($queryBlogTipo);
	$rowQueryBlogTipo = $resQueryBlogTipo -> fetch_assoc();
	//vedo se l'utente loggato è moderatore
	$queryModeratore = "SELECT moderatore FROM utente WHERE idutente = '$idutente'";
	$resModeratore = $mysqli -> query($queryModeratore);
	$rowModeratore = $resModeratore -> fetch_assoc();
}
if (isset($_SESSION["idBlog"]) && isset($_GET["id"]) && preg_match('/^[0-9]+$/i', $_GET["id"])) {
	//query che verifica se un blog esiste
	$idBlog = $_SESSION["idBlog"];
	$queryEsistenzaBlog = "SELECT idblog FROM blog WHERE idblog = '$idBlog'";
	$resEsistenzaBlog = $mysqli -> query($queryEsistenzaBlog);
	$rowEsistenzaBlog = $resEsistenzaBlog -> fetch_assoc();
	//se il blog esiste
	if($resEsistenzaBlog -> num_rows != 0) {
		//prendo il titolo del blog 
		$query = "SELECT titolo, font FROM blog WHERE idblog = '$idBlog'";
		$result = $mysqli -> query($query);
		$row = $result->fetch_assoc();
		//prendo il tema del blog
		$queryTema = "SELECT nometema, idtema FROM tema WHERE idtema IN (SELECT idtema FROM tematica WHERE idblog = '$idBlog')";
		$resTema = $mysqli -> query($queryTema);
		//prendo il creatore del blog
		$queryCreatore = "SELECT nickname, idutente FROM utente WHERE idutente = (SELECT autore FROM blog WHERE idblog = '$idBlog')";
		$resCreatore = $mysqli -> query($queryCreatore);
		$rowCreatore = $resCreatore -> fetch_assoc();
		if($resCreatore -> num_rows != 0){
			$idCreatore = $rowCreatore["idutente"];
		}
		//prendo i post del blog
		$queryStampaPost = "SELECT idpost, titolo, testo, idautore FROM post WHERE idblog = '$idBlog' order by idpost DESC";
		$resStampa = $mysqli -> query($queryStampaPost);
		//guardo se l'utente segue il blog
		if(isset($_SESSION["idutente"])){
			$querySeguito = "SELECT * FROM segueblog WHERE idblog = '$idBlog' AND idutente = '$idutente'";
			$resSeguito = $mysqli -> query($querySeguito);
		}
		//query che conta i follower del blog
		$queryFollower = "SELECT COUNT(*) as totalFollowerB FROM segueblog WHERE idblog = '$idBlog'";
		$resFollower = $mysqli -> query($queryFollower);
		$rowFollower = $resFollower -> fetch_assoc();
		//query che prende i nomi dei follower
		$queryNomeFollower = "SELECT nickname, segueblog.idutente, segueblog.idutente FROM segueblog, utente WHERE utente.idutente = segueblog.idutente AND idblog = '$idBlog'";
		$resNomeFollower = $mysqli -> query($queryNomeFollower);
		//query che prende gli id dei coautori del blog
		$queryCoautori = "SELECT nickname, idutente FROM utente, coautore WHERE idutente = idcoautore AND idblog = '$idBlog'";
		$resCoautori = $mysqli -> query($queryCoautori);
		if (isset($_SESSION["idutente"])) {
			//query che verifica se l'utente loggato è coautore del blog
			$queryVerificaC = "SELECT * FROM coautore WHERE idcoautore = '$idutente' AND idblog = '$idBlog'";
			$resVerificaC = $mysqli -> query($queryVerificaC);
		}
		//query che prende l'immagine di profilo del blog
		$querySelectImmagine = "SELECT file from multimedia, FotoBlog where FotoBlog.idBlog = '$idBlog' and idmultimedia = idFoto AND sfondo= '0'";
		$resSelectImmagine = $mysqli -> query($querySelectImmagine);
		$rowSelectImmagine = $resSelectImmagine->fetch_assoc();
		//query che prende l'immagine di sfondo del blog
		$querySelectImmagineSfondo = "SELECT file from multimedia, FotoBlog where FotoBlog.idBlog = '$idBlog' and idmultimedia = idFoto AND sfondo= '1'";
		$resSelectImmagineSfondo = $mysqli -> query($querySelectImmagineSfondo);
		$rowSelectImmagineSfondo = $resSelectImmagineSfondo->fetch_assoc();
	}
}
?>
<!DOCTYPE html>
<html>
<head>
	<?php
	//se non è settato l'id del blog o se il blog non esiste 
	if(!isset($_SESSION["idBlog"]) || !isset($_GET["id"]) || !preg_match('/^[0-9]+$/i', $_GET["id"]) || $resEsistenzaBlog -> num_rows == 0){ ?>
		<title>Blog non esistente</title>
	<?php } else { ?>
		<title><?php echo $row["titolo"];?></title>
	<?php } ?>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
	<link rel="stylesheet" type="text/css" href="Progetto_Basi.css">
	<style type="text/css">
		<?php
		//cambio l'immagine di sfondo se il blog ne ha una
		if ($resSelectImmagineSfondo -> num_rows != 0) { ?>
			#bacheca, #contenitoreCreaPost, .Posts {
				background-image: url("uploads/<?php echo $rowSelectImmagineSfondo['file'] ?>");
				background-repeat: no-repeat;
				background-size: cover;
			}
		<?php } ?>
	</style>
</head>
<body class="container">
	<div class="topnav">
		<form method="post">
			<input type="text" id="cerca" placeholder="Cerca">
		</form>
		<?php 
		if (isset($_SESSION["idutente"])) { ?>
			<a class="active" href="home2.php"><i class="glyphicon glyphicon-home"></i> Home</a>
			<a href="profilo.php?id=<?php echo $idutente?>"><i class="glyphicon glyphicon-user"></i> Profilo</a>
			<a href="crea.php"><i class="glyphicon glyphicon-plus"></i> Crea</a>
			<a href="home2.php?logout"><i class="glyphicon glyphicon-cog"></i> LogOut</a>
			<?php } else { ?>
				<a class="active" href="home2.php"><i class="glyphicon glyphicon-home"></i> Home</a>
				<a href="login.php"><i class="glyphicon glyphicon-cog"></i> LogIn/Registrati</a>
			<?php } ?>
	</div>
	<span id="usercheck"></span>
	<?php
	if(!isset($_SESSION["idBlog"]) || !isset($_GET["id"]) ||!preg_match('/^[0-9]+$/i', $_GET["id"])|| $resEsistenzaBlog -> num_rows == 0){ ?>
		<article>
			<h1 id="MieiBlog">Blog non esistente<h1>
			<img src="tryAgain.jpg" id="errImg">
		</article>
		<script type="text/javascript">
			$(document).ready(function(){
				$("#cerca").keyup(function(){
					if ($("#cerca").val() != "") {
						$("#usercheck").css("display", "block");
					} else {
						$("#usercheck").css("display", "none");
					}
				});
				//chiamata ajax per cercare all'interno del sito
				$("#cerca").keyup(function(){
					var nome = $(this).val();
					$.post(
						"checkCerca.php",
						{
							nome: nome
						},
						function(data){
							$("#usercheck").html(data);
						}
					);
				});
			});
		</script>
	<?php } else { ?>
		<article id="bacheca">
			<?php
			//se l'utente loggato è moderatore mostro l'icona per eliminare il blog
			if (isset($_SESSION["idutente"])) {
				if ($rowModeratore["moderatore"] == 1) { ?>
					<a href="home2.php" class="eliminaBlog" id="<?php echo $idBlog?>"><i class="glyphicon glyphicon-trash" style="cursor: pointer;" title="elimina blog"></i></a><br>				
				<?php }
			}?>
			<!--FONT DEL BLOG-->
			<?php 
			if ($row["font"] == "1") { ?>
				<style type="text/css">
					#bacheca, #TitoloBlog {
						font-family: Baskerville;
					}
				</style>
			<?php }
			if ($row["font"] == "2") { ?>
				<style type="text/css">
					#bacheca, #TitoloBlog {
						font-family: Architects Daughter;
					}
				</style>
			<?php }
			if ($row["font"] == "3") { ?>
				<style type="text/css">
					#bacheca, #TitoloBlog {
						font-family: Gochi Hand;
					}
				</style>
			<?php }
			if ($row["font"] == "4") { ?>
				<style type="text/css">
					#bacheca, #TitoloBlog {
						font-family: Pacifico;
					}
				</style>
				<?php }
			if ($row["font"] == "5") { ?>
				<style type="text/css">
					#bacheca, #TitoloBlog {
						font-family: Orbitron;
					}
				</style>
			<?php } ?>
			<!--IMMAGINE DEL BLOG-->
			<?php 
			if ($resSelectImmagine -> num_rows != 0) { ?>
				<img src="uploads/<?php echo $rowSelectImmagine['file'] ?>">
			<?php } else { ?>
				<p><img src="uploads/default.jpeg" class="immagineDelBlog"></p>
			<?php } ?>
			<!--NOME BLOG-->
			<h1 id="TitoloBlog"><?php echo $row["titolo"];?></h1>
			<!--TEMA BLOG-->
			<?php
			while ($rowTema = $resTema->fetch_assoc()) { ?>
				<p><a href="tema.php?id=<?php echo $rowTema["idtema"]?>"><?php echo $rowTema["nometema"]?></p></a>
			<?php }
			if($resTema -> num_rows == 0){ ?>
				<p>Tema non conforme alle linee guida della community</p>
			<?php }
			?>
			<!--FOLLOWER DEL BLOG-->
			<p class="InfoPr" id="VediFollowers">Follower: <?php echo $rowFollower["totalFollowerB"] ?></p>
			<div class="Nascondi" id="followers" style="display: none;">
				<div class="ics"><i class="glyphicon glyphicon-remove"></i></div><br>
				<div class="Lista">
					<?php
					//mostro i follower del blog
					while($rowNomeFollower = $resNomeFollower -> fetch_assoc()){ 
						$idFClick = $rowNomeFollower["idutente"]; ?>
						<p class="NomeFollower"><a href="profilo.php?id=<?php echo $rowNomeFollower["idutente"]?>"><?php echo $rowNomeFollower["nickname"]?></a></p>
						<?php 
						if (isset($_SESSION["idutente"])) {
							if($idutente == $idFClick){ ?>
								<br>
							<?php }
							//guardo se l'utente loggato segue i follower del blog
							$queryFClick = "SELECT idseguito FROM segui WHERE idfollower = '$idutente' AND idseguito='$idFClick'";
							$resFClick = $mysqli -> query($queryFClick);
							//se l'utente è premium o segue meno di 5 blog mostro il pulsante segui o non seguire
							if($rowQueryFollowerTipo["tipo"] == "premium" || $rowQueryFollowerTipo["Quanti"] < 5){
								if (isset($_SESSION["idutente"])) {
									if($resFClick -> num_rows == 0 && $idutente != $idFClick){ ?>
										<input type="submit" name="" class="seguiClick" id="<?php echo $idFClick ?>" value="segui"><br>
									<?php } 
									//se l'utente segue il blog mostro il pulsante non seguire
									elseif ($resFClick -> num_rows > 0) { ?>
										<input type="submit" name="" class="seguiClickNON" id="<?php echo $idFClick ?>" value="non seguire"><br>
									<?php }
								}
								//se l'utente è standard e segue almeno 5 blog e segue il blog mostro il pulsante non seguire
							} elseif ($rowQueryFollowerTipo["tipo"] == "standard" && $rowQueryFollowerTipo["Quanti"] >= 5) { ?>
								<?php 
								if (isset($_SESSION["idutente"])) {
									$visitor = $_SESSION["idutente"];
									if($visitor != $idFClick){ 
										if($resFClick -> num_rows != 0){ ?>
											<input type="submit" name="" class="seguiClickNON" id="<?php echo $idFClick ?>" value="non seguire"><br>
										<?php } 
									}
								}		
							}
						}
					} ?>
				</div>
			</div>
			<!--CREATORE DEL BLOG-->
			<?php
			if($resCreatore -> num_rows != 0 && $rowCreatore["nickname"] != NULL) { ?>
				<p>Creato da: <a href="profilo.php?id=<?php echo $idCreatore;?>"><?php echo $rowCreatore["nickname"];?></a></p>
			<?php } ?>
			<!--COAUTORI DEL BLOG-->
			<?php 
			if($resCoautori -> num_rows > 0){
				echo "Coautori: ";
				while ($rowCoautori = $resCoautori -> fetch_assoc()) { ?>
					<a href="profilo.php?id=<?php echo $rowCoautori["idutente"]?>"><?php echo $rowCoautori["nickname"]; ?></a>
				<?php } ?>
				<br>
				<br>
			<?php } ?>
			<!--SEGUI QUESTO BLOG-->
			<?php 
			if(isset($_SESSION["idutente"])){
				if ($rowQueryBlogTipo["tipo"] == "premium" || $rowQueryBlogTipo["Quanti"] < 5) {
					if(isset($_SESSION["idutente"])){
						if($resSeguito -> num_rows == 0){ ?>
							<input type="submit" id="Segui" value="Segui"><br>
						<?php } else { ?>
						<!--NON SEGUIRE PIÙ QUESTO BLOG-->
						<input type="submit" id="SeguiNON" value="Non seguire più"><br>
					<?php } 
					} 
				} else if($rowQueryBlogTipo["tipo"] == "standard" && $rowQueryBlogTipo["Quanti"] >= 5){ 
					if(isset($_SESSION["idutente"])){
						if($resSeguito -> num_rows != 0){ ?>
						<input type="submit" id="SeguiNON" value="Non seguire più"><br>
					<?php } 
					} 
				}
			}
			?>
			<!--CERCA NEL BLOG-->
			<form method="post"  action="cerca.php?idCercaBlog=<?php echo $idBlog ?>">
				<input class="w3-input" type="text" name="searchBlog" id="searchBlog" placeholder="Cerca nel blog" style="color: black">
				<button  type="submit" class="btn-cerca"><i class="glyphicon glyphicon-search"></i></button>
			</form>
			<!--SE CHI VISITA È L'AUTORE DEL BLOG LO PUÒ MODIFICARE E PUÒ SCRIVERE POST-->
			<?php 
			if(isset($_SESSION["idutente"])){
				if($resCreatore -> num_rows != 0 && $rowCreatore["nickname"] != NULL && $idCreatore == $_SESSION["idutente"]){ ?>
					<button class="btn-modifica"><a  href="modifica.php"><i class="glyphicon glyphicon-pencil"></i> Modifica</a></button><br>
				<?php } 
			} 
			?>
			<div id="ErroreLog">
				<?php
				if (isset($_SESSION['messaggioBlog'])) {
					echo $_SESSION['messaggioBlog'];
					unset($_SESSION['messaggioBlog']);
				} ?>
			</div>
		</article>
		<?php
		if (isset($_SESSION["idutente"])) {
			//se l'utente è creatore o coautore del blog può pubblicare un post
			if(($resCreatore -> num_rows != 0 && $rowCreatore["nickname"] != NULL && $idCreatore == $_SESSION["idutente"]) || $resVerificaC -> num_rows > 0){ ?>
			<div id="contenitoreCreaPost">
				<article>
					<h2>Crea un post</h2>
					<!--form per creare un post-->
					<form method="post" enctype="multipart/form-data" action="upload.php?id=<?php echo $idBlog?>">
						<input type="text" maxlength="40" id="titoloPost" name="titoloPost" placeholder="Titolo" class="w3-input">
						<textarea maxlength="200"  class="w3-input" id="testoPost" name="post" placeholder="A cosa stai pensando?" rows="4" cols="50"></textarea>
						<input type="file"  name="file">
						<button type="submit" name="submit" id="uploadFile" class="w3-input">Pubblica</button>
					</form>
					<div id="ErroreLog">
					<?php
						if (isset($_SESSION['erroreCaricamentoImmagine'])) {
							echo $_SESSION['erroreCaricamentoImmagine'];
							unset($_SESSION['erroreCaricamentoImmagine']);
						} ?>
					</div>
				</article>
			</div>
		<?php } 
		} ?>
		<!-- chiamate ajax per pubblicare il post -->
		<script type="text/javascript">
			$(document).ready(function(){
				//nascondo la lista dei follower del blog
				$(".ics i").click(function(){
					$(".Nascondi").css("display", "none");
				});
				$("#followers").css("display", "none");
				//mostro la lista dei follower del blog
				$("#VediFollowers").click(function(){
					$("#followers").css("display", "block");
				});
				//chiamata ajax per creare il post
				$("#uploadFile").click(function(){
					var titolo = $("#titoloPost").val();
					var testo = $("#testoPost").val();
					$.ajax({
						type: "POST",
						url: "invioPost.php",
						dataType: "html",
						data:
						{
							titolo: titolo,
							testo: testo,
							idblog: <?php echo $idBlog ?>
						}
					});
				});
			});
			// chiamata ajax per seguire i blog
			$(document).ready(function(){
				<?php 
				if (isset($_SESSION["idutente"])) { ?>
					$("#Segui").click(function(){
						location.reload();
						$.ajax({
							type: "POST",
							url: "segui.php",
							dataType: "html",
							data:
							{
								idblog: <?php echo $idBlog ?>,
								idutente: <?php echo $idutente ?>
							},
						});
					});
				<?php } 
				// chiamata ajax per NON seguire i blog
				if (isset($_SESSION["idutente"])) { ?>
					$("#SeguiNON").click(function(){
						location.reload();
						$.ajax({
							type: "POST",
							url: "segui.php",
							dataType: "html",
							data:
							{
								idfollowerNONBlog: <?php echo $idutente ?>,
								idseguitoNONBlog: <?php echo $idBlog ?>
							},
						});
					});
				<?php } ?>
			});
			//chiamata ajax per cercare il nome nel database
			$(document).ready(function(){
				$("#cerca").keyup(function(){
					var nome = $(this).val();
					$.post(
						"checkCerca.php",
						{
							nome: nome
						},
						function(data){
							$("#usercheck").html(data);
						}
					);
				});
			});
		</script>
		<?php
		if($resStampa -> num_rows > 0) {
			while ($rowStampa = $resStampa->fetch_assoc()) { 
				$IDpost = $rowStampa["idpost"];
				//query che prende l'immagine del post
				$queryStampaImmagine = "SELECT file from multimedia, foto where foto.idpost = '$IDpost' and idmultimedia = idfoto";
				$resStampaImmagine = $mysqli -> query($queryStampaImmagine);
				$rowStampaImmagine = $resStampaImmagine->fetch_assoc();
				if ($resStampaImmagine -> num_rows > 0) { 						
						$foto = $rowStampaImmagine['file'];
				}  
				//query che verifica se l'utente ha messo like a un post
				if (isset($_SESSION["idutente"])) {
					$queryCheckLike = "SELECT * FROM piaciuti WHERE idutente = '$idutente' AND idpost = '$IDpost'";
					$resCheckLike = $mysqli -> query($queryCheckLike);
				}

				//guardo quanti like ha un post
				$sqlNumeroLike = "SELECT count(*) as mipiace, idpost FROM piaciuti WHERE idpost = '$IDpost'";
				$resNumeroLike = $mysqli -> query($sqlNumeroLike);
				$rowNumeroLike = $resNumeroLike -> fetch_assoc();
				?>
				<article class="Posts" id="<?php echo $rowStampa["idpost"]?>">
					<?php
					//se un post ha un'immagine la stampo
					if ($resStampaImmagine -> num_rows > 0) { ?>						
						<img src="uploads/<?php echo $foto  ?>" class="immaginiDeiPost">
					<?php }  ?>
					<!--titolo del post-->
					<p class="titoloPost" id="titoloPostTesto"><?php echo $rowStampa["titolo"]?></p>
					<!--testo del post-->
					<p class="testoPost"><?php echo $rowStampa["testo"]?></p>
					<?php 
				if (isset($_SESSION["idutente"])) {
					//se l'utente loggato è l'autore del post lo può eliminare
					if ($idutente == $rowStampa["idautore"] || $rowModeratore["moderatore"] == 1) { ?>
						<p class="eliminaPost" id="<?php echo $rowStampa["idpost"]?>"><i class="glyphicon glyphicon-trash" title="elimina"></i></p>
					<?php } 
					//se l'utente loggato non ha messo like al post allora lo può mettere, altrimenti lo può togliere
					if ($resCheckLike -> num_rows == 0){ ?>
						<img src="heart.png" name="piaciuto" class="piaciuto" id="<?php echo $rowStampa["idpost"]?>" >
					<?php } else { ?>
						<img src="like.png" name="NONpiaciuto" class="NONpiaciuto" id="<?php echo $rowStampa["idpost"]?>">
					<?php } 
				}
				?>
				<!--numero dei like al post-->
				<p class="numeroLike"><i class="glyphicon glyphicon-heart"></i><?php echo $rowNumeroLike["mipiace"]?></p>
				<?php
				//se l'utente è loggato può commentare il post
				if (isset($_SESSION["idutente"])) { ?>
					<form method="post">
						<input type="text" class="InserisciCommento" placeholder="commento">
						<input type="button" name="btn-commenta" class="btn-commenta" id="<?php echo $rowStampa["idpost"]?>" value="invio">
					</form>
				<?php }
				//query che seleziona i commenti e il nome del commentatore
				$queryStampaCommenti = "SELECT *, nickname FROM commenti, utente WHERE idpost = '$IDpost' AND idutente = idautore";
				$resStampaCommenti = $mysqli -> query($queryStampaCommenti);
				//stampo i commenti e il nome di chi commenta
				while ($rowStampaCommenti = $resStampaCommenti -> fetch_assoc()) { ?>
					<a href="profilo.php?id=<?php echo $rowStampaCommenti["idautore"]?>" class="stampaCommenti"><?php echo $rowStampaCommenti["nickname"]?>
					</a>
					<p class="stampaCommentiTesto" id="<?php echo $rowStampaCommenti["idcommento"]?>"><?php echo $rowStampaCommenti["testo"]?></p>
					<?php 
					//se l'utente loggato è l'autore del commento lo può eliminare
					if(isset($_SESSION["idutente"])){
						if ($idutente == $rowStampaCommenti["idautore"] || $rowModeratore["moderatore"] == 1) { ?>
							<p class="eliminaCommento" id="<?php echo $rowStampaCommenti["idcommento"]?>"><i class="glyphicon glyphicon-trash" title="elimina commento"></i></p>
						<?php } 
					} 
				} ?>
				</article>
			<?php };
		}
	}?> 
	<script type="text/javascript">
		$(document).ready(function(){
			$("#cerca").keyup(function(){
				if ($("#cerca").val() != "") {
					$("#usercheck").css("display", "block");
				}else{
					$("#usercheck").css("display", "none");
				}
			});
			<?php 
			//se l'utente è loggato:
			if (isset($_SESSION["idutente"])) { ?>
				//chiamata ajax per mettere mi piace al post
				$(".piaciuto").click(function(){
					var LikeBTN = $(this).attr('id');
					location.reload();
			 		$.ajax({
			 			type: "POST",
			 			url: "segui.php",
			 			dataType: "html",
			 			data:
			 			{
			 				idutenteLike: <?php echo $idutente ?>,
			 				idBtnlike: LikeBTN
			 			}
			 		});
			 	});
			 	//chiamata ajax per togliere il like dal post
			 	$(".NONpiaciuto").click(function(){
			 		var LikeBTNNON = $(this).attr('id');
			 		location.reload();
			 		$.ajax({
			 			type: "POST",
			 			url: "segui.php",
			 			dataType: "html",
			 			data:
			 			{
			 				idutenteLikeNON: <?php echo $idutente ?>,
			 				idBtnlikeNON: LikeBTNNON
			 			}
			 		});
			 	});
			 	//chiamata ajax per inserire il commento
			 	$(".btn-commenta").click(function(){
			 		var commento = $(this).prev().val();
			 		location.reload();
			 		$.ajax({
			 			type: "POST",
			 			url: "invioPost.php",
			 			dataType: "html",
			 			data: {
			 				idpost: $(this).attr('id'),
			 				idautore: <?php echo $idutente?>,
			 				commento: commento
			 			}
			 		});
			 	});
			 	//chiamata ajax per eliminare il post
			 	$(".eliminaPost").click(function(){
			 		var EliminaP = $(this).attr('id');
			 		location.reload();
			 		$.ajax({
			 			type: "POST",
			 			url: "checkModifica.php",
			 			dataType: "html",
			 			data: 
			 			{
			 				idPostElimina: EliminaP
			 			}
			 		});
			 	});
			 	//chiamata ajax per eliminare il commento
			 	$(".eliminaCommento").click(function(){
			 		var EliminaC = $(this).attr('id');
			 		location.reload();
			 		$.ajax({
			 			type: "POST",
			 			url: "checkModifica.php",
			 			dataType: "html",
			 			data: 
			 			{
			 				idCommentoElimina: EliminaC
			 			}
			 		});
			 	});
			 	//chiamata ajax per seguire un utente dalla lista dei follower del blog
				$(".seguiClick").click(function(){
					location.reload();
					$.ajax({
						type: "POST",
						url: "segui.php",
						dataType: "html",
						data:
						{
							idfollower: <?php echo $idutente ?>,
							idseguito: $(this).attr('id')
						}
					})
				});
				//chiamata ajax per smettere di seguire un utente dalla lista dei follower del blog
				$(".seguiClickNON").click(function(){
					location.reload();
					$.ajax({
						type: "POST",
						url: "segui.php",
						dataType: "html",
						data:
						{
							idfollowerNON: <?php echo $idutente ?>,
							idseguitoNON: $(this).attr('id')
						}
					});
				});
				//chiamata ajax per eliminare il blog se l'utente loggato è moderatore
				$(".eliminaBlog").click(function(){
					$.ajax({
						type: "POST",
						url: "checkModifica.php",
						dataType: "html",
						data:
						{
							idBlogElimina: $(this).attr('id')
						}
					});
				});
			<?php } ?>			
		});
	</script>
</body>
</html>