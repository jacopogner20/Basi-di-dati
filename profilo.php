<?php
//mi connetto al database
include('connetti.php');
session_start();
if (isset($_SESSION["nome"], $_SESSION["cognome"],$_SESSION["nickname"],$_SESSION["documento"],$_SESSION["email"],$_SESSION["telefono"],$_SESSION["Bio"],$_SESSION["carta"],$_SESSION["CVC"])) {
	unset($_SESSION["nome"]);
	unset($_SESSION["cognome"]);
	unset($_SESSION["documento"]);
	unset($_SESSION["nickname"]);
	unset($_SESSION["email"]);
	unset($_SESSION["telefono"]);
	unset($_SESSION["Bio"]);
	unset($_SESSION["carta"]);
	unset($_SESSION["CVC"]);
}
if (isset($_SESSION["idutente"])) {
	$visitor = $_SESSION["idutente"];
	//query che conta quanti utente segue l'utente loggato e prende il tipo dell'utente loggato
	$queryFollowerTipo = "SELECT count(*) as Quanti, tipo FROM segui, utente where idfollower = idutente and idfollower = '$visitor'";
	$resQueryFollowerTipo = $mysqli -> query($queryFollowerTipo);
	$rowQueryFollowerTipo = $resQueryFollowerTipo -> fetch_assoc();
	//query che conta quanti blog segue l'utente loggato e prende il tipo dell'utente loggato
	$queryBlogTipo = "SELECT count(*) as QuantiBlog, tipo FROM segueblog, utente where segueblog.idutente = utente.idutente and segueblog.idutente = '$visitor'";
	$resQueryBlogTipo = $mysqli -> query($queryBlogTipo);
	$rowQueryBlogTipo = $resQueryBlogTipo -> fetch_assoc();
	//query che conta quanti temi segue l'utente loggato e prende il tipo dell'utente loggato
	$queryTemaTipo = "SELECT count(*) as QuantiTemi, tipo FROM seguetema, utente where seguetema.idutente = utente.idutente and utente.idutente= '$visitor'";
	$resQueryTemaTipo = $mysqli -> query($queryTemaTipo);
	$rowQueryTemaTipo = $resQueryTemaTipo -> fetch_assoc();
	//vedo se l'utente loggato è moderatore
	$queryModeratore = "SELECT moderatore FROM utente WHERE idutente = '$visitor'";
	$resModeratore = $mysqli -> query($queryModeratore);
	$rowModeratore = $resModeratore -> fetch_assoc();
}
if(isset($_GET["id"]) && preg_match('/^[0-9]+$/i', $_GET["id"])){
	$user = $_GET["id"];
	//prendo tutti i dati dell'utente
	$queryDati = "SELECT * FROM utente WHERE idutente = '$user'";
	$resDati = $mysqli -> query($queryDati);
	if($resDati -> num_rows > 0){
		$datiProfilo = $resDati -> fetch_assoc();
	}
	if(isset($_SESSION["idutente"])) {
		$idutente = $_SESSION["idutente"];
		$querySeguitoUt = "SELECT * FROM segui WHERE idfollower = '$idutente' AND idseguito = '$user'";
		$resSeguitoUt = $mysqli -> query($querySeguitoUt);
	}
	//query che verifica se un utente esiste
	$queryEsistenza = "SELECT idutente FROM utente WHERE idutente = '$user'";
	$resEsistenza = $mysqli -> query($queryEsistenza);
	$rowEsistenza = $resEsistenza -> fetch_assoc();
	//query che conta quanti follower ha un utente
	$queryFollower = "SELECT COUNT(*) as follower FROM segui WHERE idseguito = '$user'";
	$resFollower = $mysqli -> query($queryFollower);
	$rowFollower = $resFollower -> fetch_assoc();
	//query che stampa i nomi dei follower
	$queryFollowerNome ="SELECT nickname, segui.idfollower FROM segui, utente WHERE utente.idutente = segui.idfollower AND idseguito = '$user'";
	$resFollowerNome = $mysqli -> query($queryFollowerNome);
	//query che stampa i nomi dei seguiti
	$querySeguitiNome ="SELECT nickname, segui.idseguito FROM segui, utente WHERE utente.idutente = segui.idseguito AND idfollower = '$user'";
	$resSeguitiNome = $mysqli -> query($querySeguitiNome);
	//query che conta quanti utenti segue un utente
	$querySeguiti = "SELECT COUNT(*) as seguiti FROM segui WHERE idfollower = '$user'";
	$resSeguiti  = $mysqli -> query($querySeguiti);
	$rowSeguiti = $resSeguiti-> fetch_assoc();
	//query che conta quanti blog segue un utente
	$querySeguitiBlog = "SELECT COUNT(*) as seguitiBlog FROM segueblog WHERE idutente = '$user'";
	$resSeguitiBlog  = $mysqli -> query($querySeguitiBlog);
	$rowSeguitiBlog = $resSeguitiBlog -> fetch_assoc();
	//query che stampa i nomi dei blog che l'utente segue
	$queryNomeBlogSeguiti = "SELECT titolo, segueblog.idblog FROM segueblog, blog WHERE blog.idblog = segueblog.idblog AND idutente = '$user'";
	$resQueryNomeBlogSeguiti = $mysqli -> query($queryNomeBlogSeguiti);
	//query che conta quanti temi segue un utente
	$querySeguitiTemi = "SELECT COUNT(*) as seguitiTemi FROM seguetema WHERE idutente = '$user'";
	$resSeguitiTemi  = $mysqli -> query($querySeguitiTemi);
	$rowSeguitiTemi = $resSeguitiTemi -> fetch_assoc();
	//query che stampa i nomi dei temi che l'utente segue
	$queryNomeTemiSeguiti = "SELECT nometema, seguetema.idtema FROM seguetema, tema WHERE tema.idtema= seguetema.idtema AND idutente = '$user'";
	$resQueryNomeTemiSeguiti = $mysqli -> query($queryNomeTemiSeguiti);
	//query che prende l'immagine di profilo dell'utente
	$querySelectImmagine = "SELECT file from multimedia, FotoProfilo where FotoProfilo.idUtente = '$user' and idmultimedia = idFoto";
	$resSelectImmagine = $mysqli -> query($querySelectImmagine);
	$rowSelectImmagine = $resSelectImmagine->fetch_assoc();
}
if (isset($_SESSION["idBlog"])) {
	unset($_SESSION["idBlog"]);
}
?>
<!DOCTYPE html>
<html>
<head>
	<?php 
	if(!isset($_GET["id"]) || !preg_match('/^[0-9]+$/i', $_GET["id"])||$resEsistenza -> num_rows == 0){ ?>
		<title>Utente non esistente</title>
	<?php } else { ?>
		<title>Profilo di <?php echo $datiProfilo["nickname"]; ?></title>
	<?php } ?>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
	<link rel="stylesheet" type="text/css" href="Progetto_Basi.css">
</head>
<body class="container">
	<div class="topnav">
		<form method="post">
			<input type="text" id="cerca" placeholder="Cerca">
		</form>
		<?php 
		if (isset($_SESSION["idutente"])) { ?>
			<a class="active" href="home2.php"><i class="glyphicon glyphicon-home"></i> Home</a>
			<a href="profilo.php?id=<?php echo $visitor?>"><i class="glyphicon glyphicon-user"></i> Profilo</a>
			<a href="crea.php"><i class="glyphicon glyphicon-plus"></i> Crea</a>
			<a href="home2.php?logout"><i class="glyphicon glyphicon-cog"></i> LogOut</a>
		<?php } else { ?>
			<a class="active" href="home2.php"><i class="glyphicon glyphicon-home"></i> Home</a>
			<a href="login.php"><i class="glyphicon glyphicon-cog"></i> LogIn/Registrati</a>
		<?php } ?>
	</div>
	<span id="usercheck" ></span>
	<?php
	if(!isset($_GET["id"]) || !preg_match('/^[0-9]+$/i', $_GET["id"]) || $resEsistenza -> num_rows == 0){ ?>
		<article>
			<h1 id="MieiBlog">Utente non esistente<h1>
		</article>
		<img src="tryAgain.jpg" id="errImg">
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
		<article id="bachecaP">
			<?php
			if (isset($_SESSION["idutente"])) {
				//se l'utente loggato è modificatore può eliminare l'utente
				if ($rowModeratore["moderatore"] == 1) { ?>
					<a href="home2.php" class="eliminaUtente" id="<?php echo $user?>"><i class="glyphicon glyphicon-trash" title="elimina utente"></i></a><br>				
				<?php } 
			}
			?>
			<!--IMMAGINE DEL PROFILO-->
			<?php 
			if ($resSelectImmagine -> num_rows != 0) { ?>
				<img src="uploads/<?php echo $rowSelectImmagine['file'] ?>">
			<?php } ?>
			<!--NICKNAME-->
			<h1 id="nickname"><?php echo $datiProfilo["nickname"]?></h1>
			<!--spunta per mostrare che un utente è moderatore-->
			<?php 
			if($datiProfilo["moderatore"] == 1){?>
				<h1 id="moderatore"><i class="glyphicon glyphicon-education" title="Amministratore"></i></h1>
			<?php } ?>
			<!--spunta per vedere se un utente ha l'upgrade-->
			<?
			if ($rowFollower["follower"] >= 10) { ?>
				<h1 id="moderatore"><i class="glyphicon glyphicon-ok-circle" title="Upgrade"></i></h1>
			<?php } ?>
			<!--spunta per vedere se un utente è premium-->
			<?php 
			if ($datiProfilo["tipo"] == "premium") { ?>
				<h1 id="moderatore"><i class="glyphicon glyphicon-star-empty" title="Utente Premium"></i></h1>
			<?php } ?>
			<!--SEGUI QUESTO UTENTE-->
			<?php 
			if (isset($_SESSION["idutente"])) {
				//se l'utente loggato è premium o segue meno di 5 utenti mostro il pulsante segui o non seguire
				if($rowQueryFollowerTipo["tipo"] == "premium" || $rowQueryFollowerTipo["Quanti"] < 5){
					$visitor = $_SESSION["idutente"];
					if($visitor != $user){ 
						if($resSeguitoUt -> num_rows == 0){ ?>
							<br><input type="submit" id="SeguiUt" value="Segui"><br>
						<?php } else { ?>
							<br><input type="submit" id="SeguiNONUt" value="Non seguire più"><br>
						<?php } 
					}
				} 
				//se l'utente loggato è standard e segue almeno 5 utenti mostro il pulsante non seguire per gli utenti che segue
				else if($rowQueryFollowerTipo["tipo"] == "standard" && $rowQueryFollowerTipo["Quanti"] >= 5){
					if (isset($_SESSION["idutente"])) {
						$visitor = $_SESSION["idutente"];
						if($visitor != $user){ 
							if($resSeguitoUt -> num_rows != 0){ ?>
								<br><input type="submit" id="SeguiNONUt" value="Non seguire più"><br>
							<?php } 
						}
					}	
				}		
			}
			?>
			<p class="InfoPr" id="VediFollower">Followers: <?php echo $rowFollower["follower"]?></p>
			<p class="InfoPr" id="VediSeguiti"> Utenti seguiti: <?php echo $rowSeguiti["seguiti"]?></p>
			<p class="InfoPr" id="VediBlogSeguiti">Blog seguiti: <?php echo $rowSeguitiBlog["seguitiBlog"]?> </p>
			<p class="InfoPr" id="VediTemiSeguiti">Temi seguiti: <?php echo $rowSeguitiTemi["seguitiTemi"]?></p>
			<!--BIO-->
			<?php 
			if ($datiProfilo["bio"] != "") { ?>
				<p class="bioProfilo">"<?php echo $datiProfilo["bio"]?>"</p>
			<?php } ?>
			<!--MOSTRO I FOLLOWER DELL'UTENTE-->
			<div class="Nascondi" id="follower">
				<div class="ics"><i class="glyphicon glyphicon-remove"></i></div><br><br>
				<div class="Lista">
					<?php
					//stampo i nomi dei follower dell'utente
					while($rowFollowerNome = $resFollowerNome -> fetch_assoc()){ 
						$idFClick = $rowFollowerNome["idfollower"]; ?>
						<p class="NomeFollower"><a href="profilo.php?id=<?php echo $rowFollowerNome["idfollower"]?>"><?php echo $rowFollowerNome["nickname"]?></a></p>
						<?php
						if (isset($_SESSION["idutente"])) {
							if($idutente == $idFClick){ ?>
								<br>
							<?php }
							//guardo se l'utente loggato segue i follower dell'utente
							$queryFClick = "SELECT idseguito FROM segui WHERE idfollower = '$visitor' AND idseguito='$idFClick'";
							$resFClick = $mysqli -> query($queryFClick);
							//se l'utente è premium o segue almeno 5 utenti mostro il pulsante segui o non seguire 
							if($rowQueryFollowerTipo["tipo"] == "premium" || $rowQueryFollowerTipo["Quanti"] < 5){
								if (isset($_SESSION["idutente"])) {
									if($resFClick -> num_rows == 0 && $visitor != $idFClick){ ?>
										<input type="submit" name="" class="seguiClick" id="<?php echo $idFClick ?>" value="segui"><br>
									<?php } 
									elseif ($resFClick -> num_rows > 0) { ?>
										<input type="submit" name="" class="seguiClickNON" id="<?php echo $idFClick ?>" value="non seguire"><br>
									<?php }
								}
								//se l'utente loggato è standard mostro il tasto non seguire per gli utenti che segue
							} elseif ($rowQueryFollowerTipo["tipo"] == "standard" && $rowQueryFollowerTipo["Quanti"] >= 5) { ?>
								<?php 
								if (isset($_SESSION["idutente"])) {
									$visitor = $_SESSION["idutente"];
									if($visitor != $user || $visitor != $idFClick){ 
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
			<!--MOSTRO GLI UTENTI CHE L'UTENTE SEGUE-->
			<div class="Nascondi" id="seguiti">
				<div class="ics"><i class="glyphicon glyphicon-remove"></i></div><br>
				<div class="Lista">
					<?php
					//stampo gli utenti che seguono questo utente
					while($rowSeguitiNome = $resSeguitiNome -> fetch_assoc()){ 
						$idSClick = $rowSeguitiNome["idseguito"]; ?>
						<p class="NomeFollower"><a href="profilo.php?id=<?php echo $rowSeguitiNome["idseguito"]?>"><?php echo $rowSeguitiNome["nickname"]?></a></p>
						<?php 
						if (isset($_SESSION["idutente"])) {
							if($idutente == $idSClick){ ?>
								<br>
							<?php }
							//guardo se l'utente loggato segue gli utenti che l'utente segue
							$queryFClick = "SELECT idseguito FROM segui WHERE idfollower = '$visitor' AND idseguito='$idSClick'";
							$resFClick = $mysqli -> query($queryFClick);
							//se l'utente è premium o segue almeno 5 utenti mostro il pulsante segui o non seguire 
							if($rowQueryFollowerTipo["tipo"] == "premium" || $rowQueryFollowerTipo["Quanti"] < 5){
								if (isset($_SESSION["idutente"])) {
									if($resFClick -> num_rows == 0 && $visitor != $idSClick){ ?>
										<input type="submit" name="" class="seguiClick" id="<?php echo $idSClick ?>" value="segui"><br>
									<?php } 
									elseif ($resFClick -> num_rows > 0) { ?>
										<input type="submit" name="" class="seguiClickNON" id="<?php echo $idSClick ?>" value="non seguire"><br>
									<?php }
								}
								//se l'utente loggato è standard mostro il tasto non seguire per gli utenti che segue
							} elseif ($rowQueryFollowerTipo["tipo"] == "standard" && $rowQueryFollowerTipo["Quanti"] >= 5) { ?>
								<?php 
								if (isset($_SESSION["idutente"])) {
									$visitor = $_SESSION["idutente"];
									if($visitor != $user || $visitor != $idSClick){ 
										if($resFClick -> num_rows != 0){ ?>
											<input type="submit" name="" class="seguiClickNON" id="<?php echo $idSClick ?>" value="non seguire"><br>
										<?php } 
									}
								}		
							}
						}
					} ?>
				</div>
			</div>
			<!--LISTA DEI BLOG CHE L'UTENTE SEGUE-->
			<div class="Nascondi" id="blogs">
				<div class="ics"><i class="glyphicon glyphicon-remove"></i></div><br>
				<div class="Lista">
					<?php
					//stampo i blog che l'utente segue
					while($rowNomeBlogSeguiti = $resQueryNomeBlogSeguiti -> fetch_assoc()){ 
						$idBClick = $rowNomeBlogSeguiti["idblog"]; ?>
						<p class="NomeFollower"><a href="blog.php?id=<?php echo $rowNomeBlogSeguiti["idblog"]?>"><?php echo $rowNomeBlogSeguiti["titolo"]?></a></p>
						<?php 
						if (isset($_SESSION["idutente"])) {
							//guardo se l'utente segue i blog che l'utente segue
							$queryBClick = "SELECT idblog FROM segueblog WHERE idutente = '$visitor' AND idblog='$idBClick'";
							$resBClick = $mysqli -> query($queryBClick);
							//se l'utente è premium o segue almeno 5 utenti mostro il pulsante segui o non seguire 
							if ($rowQueryBlogTipo["tipo"] == "premium" || $rowQueryBlogTipo["QuantiBlog"] < 5) {
								if (isset($_SESSION["idutente"])) {
									if($resBClick -> num_rows == 0){ ?>
										<input type="submit" name="" class="seguiClickB" id="<?php echo $idBClick?>" value="segui"><br>
									<?php } else { ?>
										<input type="submit" name="" class="seguiClickNONB" id="<?php echo $idBClick?>"value="non seguire"><br>
									<?php }
								}
								//se l'utente loggato è standard mostro il tasto non seguire per i blog che segue
							} elseif ($rowQueryBlogTipo["tipo"] == "standard" && $rowQueryBlogTipo["QuantiBlog"] >= 5) {
								if (isset($_SESSION["idutente"])) {
									if($resBClick -> num_rows != 0){ ?>
										<input type="submit" name="" class="seguiClickNONB" id="<?php echo $idBClick?>"value="non seguire"><br>
									<?php }
								}
							}
						}
					} 
					?>
				</div>
			</div>
			<!--LISTA DEI TEMI SEGUITI DALL'UTENTE-->
			<div class="Nascondi" id="temis">
				<div class="ics"><i class="glyphicon glyphicon-remove"></i></div><br>
				<div class="Lista">
					<?php
					//stampo i temi che l'utente segue
					while($rowNomeTemiSeguiti = $resQueryNomeTemiSeguiti -> fetch_assoc()){ 
						$idTClick = $rowNomeTemiSeguiti["idtema"]; ?>
						<p class="NomeFollower"><a href="tema.php?id=<?php echo $rowNomeTemiSeguiti["idtema"]?>"><?php echo $rowNomeTemiSeguiti["nometema"]?></a></p>
						<?php 
						if (isset($_SESSION["idutente"])) {
							//guardo se l'utente loggato segue i temi che l'utente segue
							$queryTClick = "SELECT idtema FROM seguetema WHERE idutente = '$visitor' AND idtema='$idTClick'";
							$resTClick = $mysqli -> query($queryTClick);
							//se l'utente è premium o segue almeno 5 utenti mostro il pulsante segui o non seguire 
							if ($rowQueryTemaTipo["tipo"] == "premium" || $rowQueryTemaTipo["QuantiTemi"] < 5) {
								if (isset($_SESSION["idutente"])) {
									if($resTClick -> num_rows == 0){ ?>
										<input type="submit" name="" class="seguiClickT" id="<?php echo $idTClick ?> "value="segui"><br>
									<?php } else { ?>
										<input type="submit" name="" class="seguiClickNONT" id="<?php echo $idTClick ?> "value="non seguire"><br>
									<?php }
								}
								//se l'utente loggato è standard mostro il tasto non seguire per i temi che segue
							} elseif ($rowQueryTemaTipo["tipo"] == "standard" && $rowQueryTemaTipo["QuantiTemi"] >= 5) {
								if (isset($_SESSION["idutente"])) {
									if($resTClick -> num_rows != 0){ ?>
										<input type="submit" name="" class="seguiClickNONT" id="<?php echo $idTClick?>"value="non seguire"><br>
									<?php }
								}
							}
						}
					} ?>
				</div>
			</div>
			<!--CERCA NEL PROFILO-->
			<form method="post" action="cerca.php?idCerca=<?php echo $user ?>">
				<input type="text" name="searchPr" id="searchPr" class="w3-input" placeholder="Cerca nel profilo" style="color: black">
				<button type="submit" class="btn-cerca"><i class="glyphicon glyphicon-search"></i></button>
			</form>
			<!--SE IL PROFILO È DELL'UTENTE LOGGATO MOSTRO IL PULSANTE MODIFICA-->
			<?php  
			if (isset($_SESSION["idutente"])) {
				$visitor = $_SESSION["idutente"];
				if($user == $visitor){ ?> 
				<button class="btn-modifica"><a  href="modifica.php"><i class="glyphicon glyphicon-pencil"></i> Modifica</a></button><br>
			<?php } 
			}
			?> 
			<!--messaggio di errore di modifica-->
			<div id="ErroreLog">
				<?php
				if (isset($_SESSION['messaggio'])) {
					echo $_SESSION['messaggio'];
					unset($_SESSION['messaggio']);
				} ?>
			</div>
		</article>
		<?php
		//query che prende i dati relativi ai blog creati dall'utente
		$queryBlog = "SELECT blog.idblog, titolo, tema.idtema, tema.nometema FROM blog, tema, tematica WHERE tematica.idblog = blog.idblog AND tematica.idtema = tema.idtema AND autore = '$user'";
		$resBlog = $mysqli -> query($queryBlog);
		if($resBlog -> num_rows > 0){ ?>
			<!--LISTA DEI BLOG CREATI DALL'UTENTE-->
			<h1 id="MieiBlog">I miei blog</h1>
			<div class="contenitoreBlogGrande">
			<?php
			while ($rowBlog = $resBlog -> fetch_assoc()) { 
				$IDblog = $rowBlog["idblog"];
				//prendo la foto di profilo del blog
				$queryBlogFotoT = "SELECT file FROM multimedia, FotoBlog WHERE FotoBlog.idfoto = multimedia.idmultimedia AND sfondo = '0' AND FotoBlog.idBlog = '$IDblog'";
				$resBlogFotoT = $mysqli -> query($queryBlogFotoT);
				?>
				<article class="contenitoreBlog">
					<p class="titoloBlog"><a href="blog.php?id=<?php echo $rowBlog["idblog"]?>"><?php echo $rowBlog["titolo"]?></a></p>
					<?php
					$rowBlogFotoT = $resBlogFotoT -> fetch_assoc();
					if ($resBlogFotoT -> num_rows != 0) { 
						$fotoBlog = $rowBlogFotoT["file"]; ?>
						<img src="uploads/<?php echo $fotoBlog?>" class="immagineDelBlog"><br>
					<?php } else { ?>
							<p><img src="uploads/default.jpeg" class="immagineDelBlog"></p>
						<?php } ?>
					<p class="temaBlog"><a href="tema.php?id=<?php echo $rowBlog["idtema"]?>"><?php echo $rowBlog["nometema"]?></a></p>
					<?php
					if (isset($_SESSION["idutente"])) {
						//guardo se l'utente loggato segue i blog creati dall'utente
						$queryBClick = "SELECT idblog FROM segueblog WHERE idutente = '$visitor' AND idblog='$IDblog'";
						$resBClick = $mysqli -> query($queryBClick);
						if (isset($_SESSION["idutente"])) {
							//se l'utente è premium o segue almeno 5 utenti mostro il pulsante segui o non seguire 
							if ($rowQueryBlogTipo["tipo"] == "premium" || $rowQueryBlogTipo["QuantiBlog"] < 5) {
								if($resBClick -> num_rows == 0){ ?>
									<input type="submit" name="" class="Segui" id="<?php echo $IDblog?>" value="segui">
								<?php } else { ?>
									<input type="submit" name="" class="SeguiNON" id="<?php echo $IDblog?>"value="non seguire">
								<?php }
								//se l'utente loggato è standard mostro il tasto non seguire per i blog che segue
							} elseif ($rowQueryBlogTipo["tipo"] == "standard" && $rowQueryBlogTipo["QuantiBlog"] >= 5) {
								if($resBClick -> num_rows != 0){ ?>
									<input type="submit" name="" class="SeguiNON" id="<?php echo $IDblog?>"value="non seguire">
								<?php }
							}
						}
					}
					?>
				</article>
			<?php } ?>
		</div> <?php

		} ?>
		<script type="text/javascript">
		$(document).ready(function(){
			//x per nascondere la lista di follower, utenti, blog e temi seguiti
			$(".ics i").click(function(){
				$(".Nascondi").css("display", "none");
			});
			$("#follower").css("display", "none");
			$("#VediFollower").click(function(){
				$("#follower").css("display", "block");
			});
			$("#seguiti").css("display", "none");
			$("#VediSeguiti").click(function(){
				$("#seguiti").css("display", "block");
			});
			$("#blogs").css("display", "none");
			$("#VediBlogSeguiti").click(function(){
				$("#blogs").css("display", "block");
			});
			$("#temis").css("display", "none");
			$("#VediTemiSeguiti").click(function(){
				$("#temis").css("display", "block");
			});
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
				//chiamata ajax per seguire l'utente
				$("#SeguiUt").click(function(){
					location.reload();
					$.ajax({
						type: "POST",
						url: "segui.php",
						dataType: "html",
						data:
						{
							idfollower: <?php echo $visitor ?>,
							idseguito: <?php echo $user ?>
						},
					});
				});
			<?php } 
			//chiamata ajax per smettere di seguire l'utente
			if (isset($_SESSION["idutente"])) { ?>
				$("#SeguiNONUt").click(function(){
					location.reload();
					$.ajax({
						type: "POST",
						url: "segui.php",
						dataType: "html",
						data:
						{
							idfollowerNON: <?php echo $visitor ?>,
							idseguitoNON: <?php echo $user ?>
						},
					});
				});
			<?php } 
			if (isset($_SESSION["idutente"])) { ?>
				//chiamata ajax per seguire un utente dalla lista dei follower dell'utente
				$(".seguiClick").click(function(){
					location.reload();
					$.ajax({
						type: "POST",
						url: "segui.php",
						dataType: "html",
						data:
						{
							idfollower: <?php echo $visitor ?>,
							idseguito: $(this).attr('id')
						}
					})
				});
				//chiamata ajax per smettere di seguire dalla lista ddei follower dell'utente
				$(".seguiClickNON").click(function(){
					location.reload();
					$.ajax({
						type: "POST",
						url: "segui.php",
						dataType: "html",
						data:
						{
							idfollowerNON: <?php echo $visitor ?>,
							idseguitoNON: $(this).attr('id')
						}
					})
				});
				//chiamata ajax per seguire un blog dalla lista dei blog seguiti dall'utente
				$(".seguiClickB").click(function(){
					location.reload();
					$.ajax({
						type: "POST",
						url: "segui.php",
						dataType: "html",
						data:
						{
							idblog: $(this).attr('id'),
							idutente: <?php echo $visitor ?>
						},
					});
				});
				//chiamata ajax per smettere di seguire un blog dalla lista dei blog seguiti dall'utente
				$(".seguiClickNONB").click(function(){
					location.reload();
					$.ajax({
						type: "POST",
						url: "segui.php",
						dataType: "html",
						data:
						{
							idfollowerNONBlog: <?php echo $visitor ?>,
							idseguitoNONBlog: $(this).attr('id')
						}
					});
				});
				//chiamata ajax per seguire un tema dalla lista dei temi seguiti dall'utente
				$(".seguiClickT").click(function(){
					location.reload();
					$.ajax({
						type: "POST",
						url: "segui.php",
						dataType: "html",
						data:
						{
							idFollowerTema: <?php echo $visitor ?>,
							idtema: $(this).attr('id')
						}
					});
				});
				//chiamata ajax per smettere di seguire un tema dalla lista dei tema seguiti dall'utente
				$(".seguiClickNONT").click(function(){
					location.reload();
					$.ajax({
						type: "POST",
						url: "segui.php",
						dataType: "html",
						data:
						{
							idfollowerNONTema: <?php echo $visitor ?>,
							idseguitoNONTema: $(this).attr('id')
						}
					});
				});
				//chiamata ajax per seguire un blog dalla lista dei blog creati dall'utente
				$(".Segui").click(function(){
					location.reload();
					$.ajax({
						type: "POST",
						url: "segui.php",
						dataType: "html",
						data:
						{
							idblog: $(this).attr('id'),
							idutente: <?php echo $visitor ?>
						}
					});
				});
				//chiamata ajax per smettere di seguire un blog dalla lista dei blog creati dall'utente
				$(".SeguiNON").click(function(){
					location.reload();
					$.ajax({
						type: "POST",
						url: "segui.php",
						dataType: "html",
						data:
						{
							idfollowerNONBlog: <?php echo $visitor ?>,
							idseguitoNONBlog: $(this).attr('id')
						}
					});
				});
				//chiamata ajax per eliminare l'utente se l'utente loggato è moderatore
				$(".eliminaUtente").click(function(){
					$.ajax({
						type: "POST",
						url: "checkModifica.php",
						dataType: "html",
						data:
						{
							idUtenteElimina: $(this).attr('id')
						}
					});
				});
			<?php } ?>
		});
		</script>
	<?php } ?>
</body>
</html>