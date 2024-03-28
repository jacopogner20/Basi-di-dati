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
if (isset($_SESSION["idutente"])) {
	$id = $_SESSION["idutente"];
}
if (isset($_SESSION["idBlog"])) {
	unset($_SESSION["idBlog"]);
}
if(isset($_GET['logout'])){
	session_unset();
	session_destroy();
}
//mi connetto al database
include('connetti.php');
if (isset($_SESSION["idutente"])) {
	//query che prende i post dei blog che piacciono all'utente loggato
	$sqlBlogPiaciuti = "SELECT post.idpost, post.titolo, post.testo, post.idblog, post.idautore, nickname FROM post, utente WHERE idutente = idautore AND post.idblog IN (SELECT idblog from segueblog, utente WHERE segueblog.idutente = $id) order BY idpost DESC LIMIT 0,3" ;
	$resBlogPiaciuti = $mysqli -> query($sqlBlogPiaciuti);
	//query che prende i post degli utenti seguiti dall'utente loggato
	$PostDeiSeguiti = "SELECT idpost, titolo, testo, idautore, idblog, nickname FROM post, utente WHERE idutente = idautore AND idautore IN (SELECT idseguito FROM segui WHERE idfollower = '$id') ORDER BY idpost DESC LIMIT 0,6";
	$resPostSeguiti = $mysqli -> query($PostDeiSeguiti);
	//query che prende i blog dei temi che l'utente loggato segue
	$BlogTemiSeguiti = "SELECT blog.idblog, titolo, autore, nickname, tema.idtema, nometema FROM blog, tematica, utente, tema WHERE tematica.idtema = tema.idtema AND autore = idutente AND tematica.idblog = blog.idblog AND tematica.idtema IN (SELECT seguetema.idtema FROM seguetema WHERE idutente = '$id')  ORDER BY `blog`.`idblog` ASC LIMIT 0,6";
	$resBlogTemiSeguiti = $mysqli -> query($BlogTemiSeguiti);
	//query che conta quanti blog segue un utente standard
	$queryBlogTipo = "SELECT count(*) as QuantiBlog, tipo FROM segueblog, utente where segueblog.idutente = utente.idutente and segueblog.idutente = '$id'";
	$resQueryBlogTipo = $mysqli -> query($queryBlogTipo);
	$rowQueryBlogTipo = $resQueryBlogTipo -> fetch_assoc();
}

//query che prende i blog con più follower (BLOG DI TENDENZA)
$sql = "SELECT autore, nickname, titolo, blog.idblog, tema.idtema, tema.nometema, count(*) as follower from segueblog, blog, utente, tema, tematica where tematica.idtema = tema.idtema AND tematica.idblog = blog.idblog AND blog.autore = utente.idutente AND titolo IN (SELECT titolo from blog Where blog.idblog = segueblog.idblog) group by blog.idblog order by follower DESC LIMIT 0,6";
$resSql = $mysqli -> query($sql);
?>
<!DOCTYPE html>
<html>
<head>
	<?php 
	if (isset($_SESSION["idutente"])) { ?>
		<title>Home di <?php echo $_SESSION["username"]?></title>
	<?php } else { ?>
		<title>Home</title>
	<?php } ?>
	<meta charset="utf-8">
</head>
<header>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="Progetto_Basi.css">
</head>
<body class="container">
	<div class="topnav">
		<form method="post">
			<input type="text" id="cerca" placeholder="Cerca">
		</form>
		<?php
		if (isset($_SESSION["idutente"])) { ?>
			<a class="active" href="profilo.php?id=<?php echo $_SESSION["idutente"]?>"><i class="glyphicon glyphicon-user"></i> Profilo</a>
			<a href="crea.php"><i class="glyphicon glyphicon-plus"></i> Crea</a>
			<a href="home2.php?logout" id="logout"><i class="glyphicon glyphicon-cog"></i> LogOut</a>
		<?php } else { ?>
			<a class="active" href="home2.php"><i class="glyphicon glyphicon-home"></i> Home</a>
			<a href="login.php"><i class="glyphicon glyphicon-cog"></i> LogIn/Registrati</a>
		<?php } ?> 
	</div>
	<span id="usercheck"></span>
	<?php 
	if (isset($_SESSION["idutente"])) { ?>
		<!--filtro per passare da una sezione a un'altra-->
		<h1 id="welcome">Benvenuto <?php echo $_SESSION["username"]?></h1>
		<form method="post" id="FormScelta">
			<select name="scelta">
				<option name="0" value="0">Tutti i risultati</option>
				<option name="1" value="1">Tendenze</option>
				<?php
				if ($resBlogPiaciuti -> num_rows != 0) { ?>
					<option name="2" value="2">Post dei blog seguiti</option>
				<?php } 
				if ($resPostSeguiti -> num_rows != 0) { ?>
					<option name="3" value="3">Post degli utenti seguiti</option>
				<?php } 
				if ($resBlogTemiSeguiti -> num_rows != 0) { ?>
					<option name="4" value="4">Blog dei temi seguiti</option>
				<?php } ?>
			</select>
			<button type="submit" name="vai"><i class="glyphicon glyphicon-filter"></i></button>
		</form>
		<?php
		if ($resBlogPiaciuti -> num_rows == 0 && $resPostSeguiti -> num_rows == 0 && $resBlogTemiSeguiti -> num_rows == 0) { ?>
			<h2 id="MieiBlog">Inizia a seguire utenti, blog o temi</h2>
		<?php }
		if (isset($_POST["scelta"])) {
			$scelta = $_POST["scelta"];
		} else {
			$scelta = "0";
		}
		//se l'utente seleziona "Blog seguiti" o "tutti i risultati" mostro i post dei blog che l'utente segue
		if(isset($scelta) && ($scelta=="2" || $scelta=="0")){ ?>
			<?php 
			if($resBlogPiaciuti -> num_rows != 0){ ?>
				<h1 class="risultatiHome">POST DEI BLOG SEGUITI</h1>
				<?php
				//stampo i post dei blog seguiti
				while ($rowBlogPiaciuti = $resBlogPiaciuti -> fetch_assoc()) { ?>
					<div class="Posts">
						<?php
						//guardo se quel post ha una foto, se sì la stampo
						$idPostHome = $rowBlogPiaciuti["idpost"];
						$queryPostFoto = "SELECT file FROM multimedia, foto WHERE idmultimedia = idfoto AND foto.idpost = '$idPostHome'";
						$resPostFoto = $mysqli -> query($queryPostFoto);
						$rowPostFoto = $resPostFoto -> fetch_assoc();
						if ($resPostFoto -> num_rows != 0) { 
							$fotoPost = $rowPostFoto["file"]; ?>
							<img src="uploads/<?php echo $fotoPost?>" class="immaginiDeiPost"><br>
						<?php } ?>
						<p class="titoloPost"><a  href="blog.php?id=<?php echo $rowBlogPiaciuti["idblog"]?>"><?php echo $rowBlogPiaciuti["titolo"]?></a></p>

						<p class="testoPost"><?php echo $rowBlogPiaciuti["testo"]?></p>
						<p class="AutorePost"><a  href="profilo.php?id=<?php echo $rowBlogPiaciuti["idautore"]?>"><?php echo $rowBlogPiaciuti["nickname"]?></a></p> <?php
						//guardo se all'utente piace il post
						$querySeguito = "SELECT * FROM piaciuti WHERE idpost = '$idPostHome' AND idutente = '$id'";
						$resSeguito = $mysqli -> query($querySeguito);
						//guardo quanti like ha un post
						$sqlNumeroLike = "SELECT count(*) as mipiace, idpost FROM piaciuti WHERE idpost = '$idPostHome'";
						$resNumeroLike = $mysqli -> query($sqlNumeroLike);
						$rowNumeroLike = $resNumeroLike -> fetch_assoc();
						//se l'utente non ha messo like al post lo può mettere
						if($resSeguito -> num_rows == 0){ ?>
							<img src="heart.png" class="Piace"  id="<?php echo $rowBlogPiaciuti["idpost"]?>" ><img src="like.png" style="display: none;" id="nascosta"><br>
						<?php } else {//se l'utente ha messo like al post lo può togliere{ ?>
							<img src="like.png" class="PiaceNON" id="<?php echo $rowBlogPiaciuti["idpost"]?>" > 
						<?php } ?>
						<!--numero di like del post-->
						<p class="numeroLike"><i class="glyphicon glyphicon-heart"></i><?php echo $rowNumeroLike["mipiace"]?></p>
						<!--form per inserire il commmento-->
						<form method="post">
							<input type="text" class="InserisciCommento" placeholder="commento" maxlength="150">
							<input type="button" name="btn-commenta" class="btn-commenta" id="<?php echo $rowBlogPiaciuti["idpost"]?>" value="invio">
						</form>
						<?php
						//query che selezione i commenti e il nome del commentatore
						$queryStampaCommenti = "SELECT *, nickname FROM commenti, utente WHERE idpost = '$idPostHome' AND idutente = idautore";
						$resStampaCommenti = $mysqli -> query($queryStampaCommenti);
						//stampo i commenti e il nome del commentatore
						while ($rowStampaCommenti = $resStampaCommenti -> fetch_assoc()) { ?>
							<a href="profilo.php?id=<?php echo $rowStampaCommenti["idautore"]?>" class="stampaCommenti"><?php echo $rowStampaCommenti["nickname"]?></a>
							
							<p class="stampaCommentiTesto"  id="<?php echo $rowStampaCommenti["idcommento"]?>"><?php echo $rowStampaCommenti["testo"]?></p>
							<?php 
							//se l'utente loggato è creatore del commento lo può eliminare
							if ($id == $rowStampaCommenti["idautore"]) { ?>
								<p class="eliminaCommento" id="<?php echo $rowStampaCommenti["idcommento"]?>"><i class="glyphicon glyphicon-trash" title="elimina commento"></i></p><br>
							<?php } ?>
						<?php } ?>
					</div>
				<?php } 
			}
		} 
		//se l'utente seleziona "Profili seguiti" o "tutti i risultati" mostro i post degli utente che l'utente loggato segue
		if(isset($scelta) && ($scelta=="3" || $scelta=="0")){ ?>
			<!--post degli utenti che un utente segue-->
			<?php 
			if ($resPostSeguiti -> num_rows != 0) { ?>
				<h1 class="risultatiHome">POST DEGLI UTENTI SEGUITI</h1>
				<?php
				//stampo i post
				while ($rowPostSeguiti = $resPostSeguiti -> fetch_assoc()) { 
					$IDpost = $rowPostSeguiti["idpost"];
					//prendo la foto del post
					$queryPostFotoUt = "SELECT file FROM multimedia, foto WHERE foto.idfoto = multimedia.idmultimedia AND foto.idpost = '$IDpost'";
					$resPostFotoUt = $mysqli -> query($queryPostFotoUt);
					?>
					<article class="Posts">
						<?php
						$rowPostFotoUt = $resPostFotoUt -> fetch_assoc();
						if ($resPostFotoUt -> num_rows != 0) { 
							$fotoPost = $rowPostFotoUt["file"]; ?>
							<img src="uploads/<?php echo $fotoPost?>" class="immaginiDeiPost"><br>
						<?php }
						?>
						<p class="testoPost"><a href="profilo.php?id=<?php echo $rowPostSeguiti["idautore"]?>"><?php echo $rowPostSeguiti["nickname"]?></a></p>
						<p class="titoloPost"><a href="blog.php?id=<?php echo $rowPostSeguiti["idblog"]?>"><?php echo $rowPostSeguiti["titolo"]?></a></p>
						<p class="testoPost"><?php echo $rowPostSeguiti["testo"]?></p>
						<?php
						//guardo se all'utente piace il post
						$querySeguito = "SELECT * FROM piaciuti WHERE idpost = '$IDpost' AND idutente = '$id'";
						$resSeguito = $mysqli -> query($querySeguito);
						//guardo quanti like ha un post
						$sqlNumeroLike = "SELECT count(*) as mipiace, idpost FROM piaciuti WHERE idpost = '$IDpost'";
						$resNumeroLike = $mysqli -> query($sqlNumeroLike);
						$rowNumeroLike = $resNumeroLike -> fetch_assoc();
						//se l'utente non ha messo mi piace al post lo può mettere
						if($resSeguito -> num_rows == 0){ ?>
							<img src="heart.png" class="Piace"  id="<?php echo $rowPostSeguiti["idpost"]?>" ><img src="like.png" style="display: none; width: 3em;" id="nascosta"><br>
						<?php } else { ?>
							<!--se l'utente ha messo mi piace al post lo può togliere-->
							<img src="like.png" class="PiaceNON" id="<?php echo $rowPostSeguiti["idpost"]?>" > <br>
						<?php } ?>
						<!--numero dei like del post-->
						<p class="numeroLike"><i class="glyphicon glyphicon-heart"></i><?php echo $rowNumeroLike["mipiace"]?></p>
						<!--form per la creazione del commento-->
						<form method="post">
							<input type="text" class="InserisciCommento" placeholder="commento">
							<input type="button" name="btn-commenta" class="btn-commenta" id="<?php echo $rowPostSeguiti["idpost"]?>" value="invio">
						</form>
						<?php
						//query che seleziona i commenti e il nome del commentatore
						$queryStampaCommenti = "SELECT *, nickname FROM commenti, utente WHERE idpost = '$IDpost' AND idutente = idautore";
						$resStampaCommenti = $mysqli -> query($queryStampaCommenti);
						//stampo i commenti e il nome del commentatore
						while ($rowStampaCommenti = $resStampaCommenti -> fetch_assoc()) { ?>
							<a class="stampaCommenti" href="profilo.php?id=<?php echo $rowStampaCommenti["idautore"]?>"><?php echo $rowStampaCommenti["nickname"]?></a>
							<p class="stampaCommentiTesto" id="<?php echo $rowStampaCommenti["idcommento"]?>"><?php echo $rowStampaCommenti["testo"]?></p>
							<?php 
							//se l'utente loggato è l'autore del commento lo può eliminare
							if ($id == $rowStampaCommenti["idautore"]) { ?>
								<p class="eliminaCommento" id="<?php echo $rowStampaCommenti["idcommento"]?>"><i class="glyphicon glyphicon-trash"  title="elimina commento"></i></p>
							<?php } ?>
						<?php } ?>
					</article>
				<?php } 
			}
		} 
		//se l'utente seleziona "temi seguiti" o "tutti i risultati" mostro i blog dei temi che l'utente segue
		if(isset($scelta) && ($scelta=="4" || $scelta=="0")){ ?>
			<?php 
			if($resBlogTemiSeguiti -> num_rows != 0){ ?>
				<h1 class="risultatiHome">BLOG DEI TEMI SEGUITI</h1>
				<div class="contenitoreBlogGrande">
					<?php
					while ($rowBlogTemiSeguiti = $resBlogTemiSeguiti -> fetch_assoc()) { 
						$IDblog = $rowBlogTemiSeguiti["idblog"];
						//prendo la foto di profilo del blog
						$queryBlogFotoT = "SELECT file FROM multimedia, FotoBlog WHERE FotoBlog.idfoto = multimedia.idmultimedia AND FotoBlog.idBlog = '$IDblog' AND sfondo = 0";
						$resBlogFotoT = $mysqli -> query($queryBlogFotoT);
						?>
						<article class="contenitoreBlog">
							<p class="titoloBlog"><a href="blog.php?id=<?php echo $rowBlogTemiSeguiti["idblog"]?>"><?php echo $rowBlogTemiSeguiti["titolo"]?></a></p>
							<?php
							$rowBlogFotoT = $resBlogFotoT -> fetch_assoc();
							//se il blog ha una foto profilo la stampo
							if ($resBlogFotoT -> num_rows != 0) { 
								$fotoBlog = $rowBlogFotoT["file"]; ?>
								<img src="uploads/<?php echo $fotoBlog?>" class="immagineDelBlog"><br>
							<?php } else { ?>
							<p><img src="uploads/default.jpeg" class="immagineDelBlog"></p>
							<?php } ?>
							<p class="temaBlog"><a href="tema.php?id=<?php echo $rowBlogTemiSeguiti["idtema"]?>"><?php echo $rowBlogTemiSeguiti["nometema"]?></a></p>
							<p class="autoreBlog"><a href="profilo.php?id=<?php echo $rowBlogTemiSeguiti["autore"]?>"><?php echo $rowBlogTemiSeguiti["nickname"]?></a></p>
							<?php
							//controllo se l'utente loggato segue il blog
							$queryBClick = "SELECT idblog FROM segueblog WHERE idutente = '$id' AND idblog='$IDblog'";
							$resBClick = $mysqli -> query($queryBClick);
							//se l'utente loggato è premium o segue meno di 5 blog mostro il pulsante segui o non seguire
							if ($rowQueryBlogTipo["tipo"] == "premium" || $rowQueryBlogTipo["QuantiBlog"] < 5) {
								if (isset($_SESSION["idutente"])) {
									if($resBClick -> num_rows == 0){ ?>
										<input type="button" name="" class="Segui" id="<?php echo $IDblog?>" value="segui">
									<?php } else { ?>
										<input type="button" name="" class="SeguiNON" id="<?php echo $IDblog?>"value="non seguire">
									<?php }
								}
							//se l'utente loggato è standard e segue almento 5 blog mostro il tasto non seguire per i blog che segue
							} elseif ($rowQueryBlogTipo["tipo"] == "standard" && $rowQueryBlogTipo["QuantiBlog"] >= 5) {
								if (isset($_SESSION["idutente"])) {
									if($resBClick -> num_rows != 0){ ?>
										<input type="submit" name="" class="SeguiNON" id="<?php echo $IDblog?>"value="non seguire">
									<?php }
								}
							}
							?>
						</article>
					<?php } ?>
				</div>
			<?php }
		} 
	}
	//se l'utente seleziona "Tendenze" o "tutti i risultati" mostro i blog di tendenza
	if((isset($scelta) && ($scelta == "1" || $scelta == "0")) || !isset($_SESSION["idutente"])){ ?>
		<h1 id="tendenza" class="risultatiHome">BLOG DI TENDENZA</h1> 
		<div class="contenitoreBlogGrande">
			<?php
			while ($rowSql = $resSql -> fetch_assoc()){ 
				$IDblog = $rowSql["idblog"]; 
				//seleziono l'immagine profilo del blog
				$queryBlogFotoTendenze = "SELECT file FROM multimedia, FotoBlog WHERE FotoBlog.idfoto = multimedia.idmultimedia AND FotoBlog.idBlog = '$IDblog' AND sfondo = '0'";
				$resBlogFotoTendenze = $mysqli -> query($queryBlogFotoTendenze);
				?>
				<article class="contenitoreBlog">
					<p class="titoloBlog"><a href="blog.php?id=<?php echo $rowSql["idblog"]?>"><?php echo $rowSql["titolo"]?></a></p>
					<?php 
					//Se il blog ha una foto profilo la mostro
					$rowFotoBlogTendenze = $resBlogFotoTendenze -> fetch_assoc();
					if ($resBlogFotoTendenze -> num_rows != 0) { 
						$fotoBlogTend = $rowFotoBlogTendenze["file"];
						?>
						<img src="uploads/<?php echo $fotoBlogTend?>" class="immagineDelBlog">
					<?php } else { ?>
							<p><img src="uploads/default.jpeg" class="immagineDelBlog"></p>
						<?php } ?>
					<p class="temaBlog"><a href="tema.php?id=<?php echo $rowSql["idtema"]?>"><?php echo $rowSql["nometema"]?></a></p>
					<p class="autoreBlog"><a href="profilo.php?id=<?php echo $rowSql["autore"]?>"><?php echo $rowSql["nickname"]?></a></p>
					<?php
					if (isset($_SESSION["idutente"])) {
						//guardo se l'utente loggato segue quel blog
						$queryBClick = "SELECT idblog FROM segueblog WHERE idutente = '$id' AND idblog='$IDblog'";
						$resBClick = $mysqli -> query($queryBClick);
						//se l'utente è premium o segue meno di 5 blog mostro i pulsanti segui o non seguire
						if ($rowQueryBlogTipo["tipo"] == "premium" || $rowQueryBlogTipo["QuantiBlog"] < 5) {
							if($resBClick -> num_rows == 0){ ?>
								<input type="button" name="" class="Segui" id="<?php echo $IDblog?>" value="segui">
							<?php } else { ?>
								<input type="button" name="" class="SeguiNON" id="<?php echo $IDblog?>"value="non seguire">
							<?php }
							//se l'utente è standard o segue meno di 5 blog mostro il pulsante non seguire per i blog che segue
						} elseif ($rowQueryBlogTipo["tipo"] == "standard" && $rowQueryBlogTipo["QuantiBlog"] >= 5) {
							if (isset($_SESSION["idutente"])) {
								if($resBClick -> num_rows != 0){ ?>
									<input type="submit" name="" class="SeguiNON" id="<?php echo $IDblog?>"value="non seguire">
								<?php }
							}
						}
					}
					?>
				</article>
			<?php } ?>
		</div>	
	<?php } ?>
	<hr>
	<footer>
		<p>Sito realizzato da Alessio Siragusa (matricola 578426) e da Jacopo Gneri (matricola 581536) <br> 
		per l'esame di Basi di Dati dell'a.a. 2019/2020</p>
	</footer>
	<script type="text/javascript">
		//chiamata ajax per cercare il nome nel database
		$(document).ready(function(){
			$("#cerca").keyup(function(){
				if ($("#cerca").val() != "") {
					$("#usercheck").css("display", "block");
				}else{
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
			<?php 
			if (isset($_SESSION["idutente"])) { ?>
				//chiamata ajax per mettere like al post
				$(".Piace").click(function(){
					var idPost = $(this).attr('id');
					location.reload();
					$.ajax({
						type: "POST",
						url: "segui.php",
						dataType: "html",
						data:
						{
							idPostHome: idPost,
							idutenteHome: <?php echo $id ?>
						},
					});
				});
				// chiamata ajax per togliere like ad un post
				$(".PiaceNON").click(function(){
					var idPost = $(this).attr('id');
					location.reload();
					$.ajax({
						type: "POST",
						url: "segui.php",
						dataType: "html",
						data:
						{
							idfollowerNONPostHome: <?php echo $id ?>,
							idseguitoNONPostHome: idPost
						},
					});
				});
				//chiamata ajax per scrivere un commento
			 	$(".btn-commenta").click(function(){
			 		var commento = $(this).prev().val();
			 		location.reload();
			 		$.ajax({
			 			type: "POST",
			 			url: "invioPost.php",
			 			dataType: "html",
			 			data: 
			 			{
			 				idpost: $(this).attr('id'),
			 				idautore: <?php echo $id?>,
			 				commento: commento
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
			 	//chiamata ajax per seguire un blog
				$(".Segui").click(function(){
					location.reload();
					$.ajax({
						type: "POST",
						url: "segui.php",
						dataType: "html",
						data:
						{
							idblog: $(this).attr('id'),
							idutente: <?php echo $id ?>
						}
					});
				});
				//chiamata ajax per smettere di seguire un blog
				$(".SeguiNON").click(function(){
					location.reload();
					$.ajax({
						type: "POST",
						url: "segui.php",
						dataType: "html",
						data:
						{
							idfollowerNONBlog: <?php echo $id ?>,
							idseguitoNONBlog: $(this).attr('id')
						}
					});
				});
			<?php } ?>
		});
	</script>
</body>
</html>