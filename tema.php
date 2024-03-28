<?php
session_start();
//mi connetto al database
include('connetti.php');
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
if (isset($_SESSION["idBlog"])) {
	unset($_SESSION["idBlog"]);
}
if (isset($_GET["id"]) && preg_match('/^[0-9]+$/i', $_GET["id"])) {
	$idTema = $_GET["id"];
	//query che verifica se un tema esiste
	$queryEsistenzaTema = "SELECT idtema FROM tema WHERE idtema = '$idTema'";
	$resEsistenzaTema = $mysqli -> query($queryEsistenzaTema);
	$rowEsistenzaTema = $resEsistenzaTema -> fetch_assoc();
}
if (isset($_SESSION["idutente"]) && isset($_GET["id"]) && preg_match('/^[0-9]+$/i', $_GET["id"])) {
	$idutente = $_SESSION["idutente"];
	//guardo se l'utente loggato segue il tema
	$queryFollowTema = "SELECT * FROM seguetema WHERE idutente = '$idutente' AND idtema = '$idTema'";
	$resSeguitoTema = $mysqli -> query($queryFollowTema);
	//query che controlla quanti utenti segue l'utente loggato e il tipo dell'utente loggato
	$queryFollowerTipo = "SELECT count(*) as Quanti, tipo FROM segui, utente where idfollower = idutente and idfollower = '$idutente'";
	$resQueryFollowerTipo = $mysqli -> query($queryFollowerTipo);
	$rowQueryFollowerTipo = $resQueryFollowerTipo -> fetch_assoc();
	//query che controlla quanti blog segue l'utente loggato e il tipo dell'utente loggato
	$queryBlogTipo = "SELECT count(*) as QuantiBlog, tipo FROM segueblog, utente where segueblog.idutente = utente.idutente and segueblog.idutente = '$idutente'";
	$resQueryBlogTipo = $mysqli -> query($queryBlogTipo);
	$rowQueryBlogTipo = $resQueryBlogTipo -> fetch_assoc();
	//query che controlla quanti temi segue l'utente loggato e il tipo dell'utente loggato
	$queryTemaTipo = "SELECT count(*) as Quanti, tipo FROM seguetema, utente where seguetema.idutente = utente.idutente and utente.idutente= '$idutente'";
	$resQueryTemaTipo = $mysqli -> query($queryTemaTipo);
	$rowQueryTemaTipo = $resQueryTemaTipo -> fetch_assoc();
	//vedo se l'utente loggato è moderatore
	$queryModeratore = "SELECT moderatore FROM utente WHERE idutente = '$idutente'";
	$resModeratore = $mysqli -> query($queryModeratore);
	$rowModeratore = $resModeratore -> fetch_assoc();
}
if (isset($_GET["id"]) && preg_match('/^[0-9]+$/i', $_GET["id"])) {
	//prendo il nome del tema
	$queryNomeTema = "SELECT nometema FROM tema WHERE idtema = '$idTema'";
	$resNomeTema = $mysqli -> query($queryNomeTema);
	$rowTema = $resNomeTema -> fetch_assoc();
	//cerco quanti blog hanno quel tema
	$queryQuantiBlog = "SELECT COUNT(*) as total FROM tematica WHERE idtema = '$idTema'";
	$resQuantiBlog = $mysqli -> query($queryQuantiBlog);
	$rowQntBlog = $resQuantiBlog -> fetch_assoc();
	//cerco quanti utenti seguono questo tema
	$queryQuantiUtenti = "SELECT COUNT(*) as totalFollower FROM seguetema WHERE idtema = '$idTema'";
	$resQuantiUtenti = $mysqli -> query($queryQuantiUtenti);
	$rowQuantiUtenti = $resQuantiUtenti -> fetch_assoc();
	//query che prende i blog che hanno questo tema
	$queryBlog = "SELECT blog.idblog, blog.autore, blog.titolo, nickname FROM blog, tematica, utente WHERE blog.autore = utente.idutente AND blog.idblog = tematica.idblog AND idtema = '$idTema' ORDER BY idblog DESC";
	$resBlog = $mysqli -> query($queryBlog);
	//query che prende i nomi dei follower del tema
	$queryNomeFollower = "SELECT nickname, seguetema.idutente FROM seguetema, utente WHERE utente.idutente = seguetema.idutente AND idtema = '$idTema'";
	$resNomeFollower = $mysqli -> query($queryNomeFollower);
	//query che prende i nomi dei blog che hanno questo tema
	$queryNomeBlog = "SELECT titolo, tematica.idblog FROM tematica, blog WHERE blog.idblog = tematica.idblog AND idtema = '$idTema'";
	$resNomeBlog = $mysqli -> query($queryNomeBlog);
}
?>
<!DOCTYPE html>
<html>
<head>
	<?php 
	//se l'id non è settato o non esiste non mostro il tema
	if(!isset($_GET["id"]) || !preg_match('/^[0-9]+$/i', $_GET["id"])|| $resEsistenzaTema -> num_rows == 0){ ?>
		<title>Tema non esistente</title>
	<?php } else { ?>
		<title>Tema <?php echo $rowTema["nometema"]?></title>
	<?php } ?>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
	<link rel="stylesheet" type="text/css" href="Progetto_Basi.css">
	<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
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
	if(!isset($_GET["id"]) || !preg_match('/^[0-9]+$/i', $_GET["id"])|| $resEsistenzaTema -> num_rows == 0){ ?>
		<article>
			<h1 id="MieiBlog">Tema non esistente<h1>
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
				//chiamata ajax per la ricerca all'interno del sito
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
			//se l'utente è moderatore può eliminare il tema
			if(isset($_SESSION["idutente"])){
				if ($rowModeratore["moderatore"] == 1) { ?>
					<a href="home2.php" class="eliminaTema" id="<?php echo $idTema?>"><i class="glyphicon glyphicon-trash" title="elimina tema"></i></a><br>				
				<?php } 
			}
			?>
			<!--NOME TEMA-->
			<h1 class="titoloTema"><?php echo $rowTema["nometema"]?></h1>
			<!--follower del tema-->
			<p class="InfoPr" id="VediFollowers">Follower: <?php echo $rowQuantiUtenti["totalFollower"] ?> </p>
			<!--blog con questo tema-->
			<p class="InfoPr" id="VediBlogs"> Blog con questo tema: <?php echo $rowQntBlog["total"];?></p>
			<!--LISTA DEI FOLLOWER DEL TEMA-->
			<div class="Nascondi" id="followers">
				<div class="ics"><i class="glyphicon glyphicon-remove"></i></div><br>
				<div class="Lista">
					<?php
					//mostro i follower del tema
					while($rowNomeFollower = $resNomeFollower -> fetch_assoc()){ 
						$idFClick = $rowNomeFollower["idutente"] ?>
						<p class="NomeFollower"><a href="profilo.php?id=<?php echo $rowNomeFollower["idutente"]?>"><?php echo $rowNomeFollower["nickname"]?></a></p>
						<?php 
						if (isset($_SESSION["idutente"])) {
							if($idutente == $idFClick){ ?>
								<br>
							<?php }
							//guardo se l'utente loggato segue i follower del tema
							$queryFClick = "SELECT idseguito FROM segui WHERE idfollower = '$idutente' AND idseguito='$idFClick'";
							$resFClick = $mysqli -> query($queryFClick);
							//se l'utente è premium o segue meno di 5 blog mostro il pulsante segui o non seguire
							if($rowQueryFollowerTipo["tipo"] == "premium" || $rowQueryFollowerTipo["Quanti"] < 5){
								if (isset($_SESSION["idutente"])) {
									if($resFClick -> num_rows == 0 && $idutente != $idFClick){ ?>
										<input type="submit" name="" class="seguiClick" id="<?php echo $idFClick ?>" value="segui"><br>
									<?php } 
									//se l'utente segue il tema mostro il pulsante non seguire
									elseif ($resFClick -> num_rows > 0) { ?>
										<input type="submit" name="" class="seguiClickNON" id="<?php echo $idFClick ?>" value="non seguire"><br>
									<?php }
								}
								//se l'utente è standard e segue almeno 5 temi e segue il tema mostro il pulsante non seguire
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
			<!--LISTA DEI BLOG CON QUESTO TEMA-->
			<div class="Nascondi" id="blogs">
				<div class="ics"><i class="glyphicon glyphicon-remove"></i></div><br>
				<div class="Lista">
					<?php
					//mostro i blog con questo tema
					while($rowNomeBlog = $resNomeBlog -> fetch_assoc()){ 
						$idBClick = $rowNomeBlog["idblog"]; ?>
						<p class="NomeFollower"><a href="blog.php?id=<?php echo $rowNomeBlog["idblog"]?>"><?php echo $rowNomeBlog["titolo"]?></a></p>
						<?php 
						if (isset($_SESSION["idutente"])) {
							//guardo se l'utente loggato segue i blog con questo tema
							$queryBClick = "SELECT idblog FROM segueblog WHERE idutente = '$idutente' AND idblog='$idBClick'";
							$resBClick = $mysqli -> query($queryBClick);
							//se l'utente è premium o segue meno di 5 blog mostro il pulsante segui o non seguire
							if ($rowQueryBlogTipo["tipo"] == "premium" || $rowQueryBlogTipo["QuantiBlog"] < 5) {
								if (isset($_SESSION["idutente"])) {
									if($resBClick -> num_rows == 0){ ?>
										<input type="submit" name="" class="seguiClickB" id="<?php echo $idBClick?>" value="segui"><br>
									<?php } else { ?>
										<input type="submit" name="" class="seguiClickNONB" id="<?php echo $idBClick?>"value="non seguire"><br>
									<?php }
								}
								//se l'utente è standard e segue almeno 5 blog mostro il pulsante non seguire
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
			<!--SEGUI QUESTO TEMA-->
			<?php 
			if (isset($_SESSION["idutente"])) { 
				//se l'utente è premium o segue meno di 5 temi mostro il pulsante segui o non seguire
				if ($rowQueryTemaTipo["tipo"] == "premium" || $rowQueryTemaTipo["Quanti"] < 5) {
					if (isset($_SESSION["idutente"])) { 
						if($resSeguitoTema -> num_rows == 0) { ?>
							<input type="submit" id="SeguiTema" value="Segui" ><br>
						<?php } else { ?>
							<input type="submit" id="SeguiNONTema" value="Non seguire più" ><br>
						<?php } 
					} 
					//se l'utente è standard e segue almeno 5 temi mostro il pulsante non seguire se segue il tema
				} else if($rowQueryTemaTipo["tipo"] == "standard" && $rowQueryTemaTipo["Quanti"] >= 5){ 
					if (isset($_SESSION["idutente"])) { 
						if($resSeguitoTema -> num_rows != 0) { ?>
							<input type="submit" id="SeguiNONTema" value="Non seguire più" ><br>
						<?php } 
					} 
				}
			}
			?>
			<!--CERCA NEL TEMA-->
			<form method="post" action="cerca.php?idCercaTema=<?php echo $idTema ?>">
				<input type="text" name="searchTema" id="searchTema" placeholder="Cerca nel tema" class="w3-input" style="color: black">
				<button type="submit" class="btn-cerca" ><i class="glyphicon glyphicon-search"></i></button>
			</form>
		</article>
		<div>
		<?php
		//tutti i blog con questo tema 
		if($resBlog -> num_rows > 0) { ?>
			<h1 id="MieiBlog">Blog con questo tema</h1>
			<div class="contenitoreBlogGrande">
				<?php
				//stampo i blog con questo tema
				while ($rowBlog = $resBlog -> fetch_assoc()) { 
					//guardo se l'utente segue il blog
					$idBlog = $rowBlog["idblog"];
					if (isset($_SESSION["idutente"])) {
						//guardo se l'utente segue i blog con quel tema
						$querySeguito = "SELECT * FROM segueblog WHERE idblog = '$idBlog' AND idutente = '$idutente'";
						$resSeguito = $mysqli -> query($querySeguito); 
					}
					//prendo l'immagine di profilo del blog
					$queryFotoBlog = "SELECT file FROM FotoBlog, multimedia WHERE idmultimedia = idFoto AND idBlog = '$idBlog' AND sfondo = '0'";
					$resFotoBlog = $mysqli -> query($queryFotoBlog);
					$rowFotoBlog = $resFotoBlog -> fetch_assoc(); 
					?>
					<article class="contenitoreBlog">
						<?php
						if ($resFotoBlog -> num_rows != 0) { ?>
							<p><img src="uploads/<?php echo $rowFotoBlog["file"]?>" class="immagineDelBlog"></p>
						<?php } else { ?>
							<p><img src="uploads/default.jpeg" class="immagineDelBlog"></p>
							<?php } ?>
						<p class="titoloBlog"><a href="blog.php?id=<?php echo $rowBlog["idblog"]?>"><?php echo $rowBlog["titolo"]?></a></p>
						<p class="autoreBlog"><a href="profilo.php?id=<?php echo $rowBlog["autore"]?>"><?php echo $rowBlog["nickname"]?></a></p>
						<?php
						if (isset($_SESSION["idutente"])) {
							//guardo se l'utente segue i blog
							$queryBClick = "SELECT idblog FROM segueblog WHERE idutente = '$idutente' AND idblog='$idBlog'";
							$resBClick = $mysqli -> query($queryBClick);
							//se l'utente è premium o segue meno di 5 blog mostro il pulsante segui o non seguire
							if ($rowQueryBlogTipo["tipo"] == "premium" || $rowQueryBlogTipo["QuantiBlog"] < 5) {
								if($resBClick -> num_rows == 0){ ?>
									<input type="submit" name="" class="Segui" id="<?php echo $idBlog?>" value="segui">
								<?php } else { ?>
									<input type="submit" name="" class="SeguiNON" id="<?php echo $idBlog?>"value="non seguire">
								<?php }
								//se l'utente è standard e segue almeno di 5 blog mostro il pulsante non seguire
							} elseif ($rowQueryBlogTipo["tipo"] == "standard" && $rowQueryBlogTipo["QuantiBlog"] >= 5) {
								if (isset($_SESSION["idutente"])) {
									if($resBClick -> num_rows != 0){ ?>
										<input type="submit" name="" class="SeguiNON" id="<?php echo $idBlog?>"value="non seguire">
									<?php }
								}
							}
						}
						?>
					</article>
				<?php } ?>
			</div>
		<?php 
		}
		?>
		</div>
		<script type="text/javascript">
			$(document).ready(function(){
				$(".ics i").click(function(){
					$(".Nascondi").css("display", "none");
				});
				$("#followers").css("display", "none");
				$("#VediFollowers").click(function(){
					$("#followers").css("display", "block");
				});
				$("#blogs").css("display", "none");
				$("#VediBlogs").click(function(){
					$("#blogs").css("display", "block");
				});
				$("#cerca").keyup(function(){
					if ($("#cerca").val() != "") {
						$("#usercheck").css("display", "block");
					}else{
						$("#usercheck").css("display", "none");
					}
				});
				//chiamata ajax per cercare i nomi nel sito
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
				//chiamata ajax per seguire il tema 
				if (isset($_SESSION["idutente"])) { ?>
					$("#SeguiTema").click(function(){
						location.reload();
						$.ajax({
							type: "POST",
							url: "segui.php",
							dataType: "html",
							data:
							{
								idFollowerTema: <?php echo $_SESSION["idutente"] ?>,
								idtema: <?php echo  $idTema ?>
							}
						});
					});	
				<?php } 
				//chiamata ajax per smettere di seguire il tema
				if (isset($_SESSION["idutente"])) { ?>
					$("#SeguiNONTema").click(function(){
						location.reload();
						$.ajax({
							type: "POST",
							url: "segui.php",
							dataType: "html",
							data:
							{
								idfollowerNONTema: <?php echo $idutente ?>,
								idseguitoNONTema: <?php echo $idTema ?>
							}
						});
					});
					// chiamata ajax per seguire i blog
					$(".Segui").click(function(){
						location.reload();
						$.ajax({
							type: "POST",
							url: "segui.php",
							dataType: "html",
							data:
							{
								idblog: $(this).attr('id'),
								idutente: <?php echo $idutente ?>
							},
						});
					});
					//chiamata ajax per smettere di seguire i blog
					$(".SeguiNON").click(function(){
						location.reload();
						$.ajax({
							type: "POST",
							url: "segui.php",
							dataType: "html",
							data:
							{
								idfollowerNONBlog: <?php echo $idutente ?>,
								idseguitoNONBlog: $(this).attr('id')
							},
						});
					});
					//chiamata ajax per seguire un follower del tema
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
						});
					});
					//chiamata ajax per smettere di seguire un follower del tema
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
					//chiamata ajax per seguire un blog dalla lista dei blog con questo tema
					$(".seguiClickB").click(function(){
						location.reload();
						$.ajax({
							type: "POST",
							url: "segui.php",
							dataType: "html",
							data:
							{
								idblog: $(this).attr('id'),
								idutente: <?php echo $idutente ?>
							}
						});
					});
					//chiamata ajax per smettere di seguire un blog dalla lista dei blog con questo tema
					$(".seguiClickNONB").click(function(){
						location.reload();
						$.ajax({
							type: "POST",
							url: "segui.php",
							dataType: "html",
							data:
							{
								idfollowerNONBlog: <?php echo $idutente ?>,
								idseguitoNONBlog: $(this).attr('id')
							}
						});
					});
					//chiamata ajax per eliminare il tema se l'utente è moderatore
					$(".eliminaTema").click(function(){
					$.ajax({
						type: "POST",
						url: "checkModifica.php",
						dataType: "html",
						data:
						{
							idTemaElimina: $(this).attr('id')
						}
					});
				});
				<?php } ?>		
			});
		</script>
	<?php } ?>
</body>
</html>