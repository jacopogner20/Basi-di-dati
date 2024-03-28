<?php 
session_start();
if (isset($_SESSION["idutente"])) {
	$idutente = $_SESSION["idutente"];
}
if (isset($_SESSION["idBlog"])) {
	unset($_SESSION["idBlog"]);
}
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
if (isset($_SESSION["idutente"])) {
	//query che conta quanti blog segue un utente e il tipo dell'utente
	$queryBlogTipo = "SELECT count(*) as QuantiBlog, tipo FROM segueblog, utente where segueblog.idutente = utente.idutente and segueblog.idutente = '$idutente'";
	$resQueryBlogTipo = $mysqli -> query($queryBlogTipo);
	$rowQueryBlogTipo = $resQueryBlogTipo -> fetch_assoc();
}
if (isset($_GET["idCerca"], $_POST["searchPr"])) {
	$idCerca = $_GET["idCerca"];
	$cercaPR = htmlspecialchars($_POST["searchPr"]);
	//cerco i post all'interno dell'autore del post
	$queryCercaPOST = $mysqli -> prepare("SELECT titolo, testo, idblog, idautore, post.idpost FROM post WHERE idautore = '$idCerca' AND  (testo LIKE CONCAT('%',?,'%') OR titolo LIKE CONCAT('%',?,'%')) ORDER BY idpost DESC");
	$queryCercaPOST -> bind_param('ss', $cercaPR, $cercaPR);
	$queryCercaPOST -> execute();
	$resCercaPOST = $queryCercaPOST -> get_result();
	//cerco i blog all'interno dell'autore del blog
	$queryCercaBLOG = $mysqli -> prepare("SELECT titolo, idblog FROM blog WHERE autore = '$idCerca' AND titolo LIKE CONCAT('%',?,'%')");
	$queryCercaBLOG -> bind_param('s', $cercaPR);
	$queryCercaBLOG -> execute();
	$resCercaBLOG = $queryCercaBLOG -> get_result();
}
else if (isset($_GET["idCercaBlog"], $_POST["searchBlog"])) {
	$idCercaBlog = $_GET["idCercaBlog"];
	$cercaBL = htmlspecialchars($_POST["searchBlog"]);
	//cerco i post all'interno del blog
	$queryCercaInBlog = $mysqli -> prepare("SELECT titolo, testo, idblog, post.idautore, post.idpost FROM post WHERE idblog = '$idCercaBlog' AND (testo LIKE CONCAT('%',?,'%') OR titolo LIKE CONCAT('%',?,'%')) ORDER BY idpost DESC");
	$queryCercaInBlog -> bind_param('ss', $cercaBL, $cercaBL);
	$queryCercaInBlog -> execute();
	$resCercaInBlog = $queryCercaInBlog -> get_result();
}
else if (isset($_GET["idCercaTema"], $_POST["searchTema"])) {
	$idCercaTema = $_GET["idCercaTema"];
	$cercaT = htmlspecialchars($_POST["searchTema"]);
	//cerco i blog all'interno del tema
	$queryBlogTema = $mysqli -> prepare("SELECT titolo, idblog, autore, nickname FROM blog, utente WHERE (titolo LIKE CONCAT('%',?,'%') OR nickname LIKE CONCAT('%',?,'%')) AND idblog IN (SELECT idblog FROM tematica WHERE idtema = '$idCercaTema') AND idutente = autore");
	$queryBlogTema -> bind_param('ss', $cercaT, $cercaT);
	$queryBlogTema -> execute();
	$resBlogTema = $queryBlogTema -> get_result();
} 
?>
<!DOCTYPE html>
<html>
<head>
	<title>Risultati ricerca</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
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
	//se ci sono risultati corretti
	if((isset($_GET["idCerca"], $_POST["searchPr"]))||(isset($_GET["idCercaBlog"], $_POST["searchBlog"])) ||(isset($_GET["idCercaTema"], $_POST["searchTema"]))){ 
		if (isset($_GET["idCerca"], $_POST["searchPr"])) { ?>
			<h1 id="MieiBlog">Risultati per '<?php echo $cercaPR ?>'</h1>
		<?php	}
		if (isset($_GET["idCercaBlog"], $_POST["searchBlog"])) { ?>
			<h1 id="MieiBlog">Risultati per '<?php echo $cercaBL ?>'</h1>
		<?php }
		if (isset($_GET["idCercaTema"], $_POST["searchTema"])) { ?>
			<h1 id="MieiBlog">Risultati per '<?php echo $cercaT ?>'</h1>
		<?php }
		if (isset($_GET["idCerca"], $_POST["searchPr"])) { 
			if ($resCercaBLOG -> num_rows != 0) { ?>
				<h1 id="MieiBlog">Blog</h1>
			<?php } ?>
			<div class="contenitoreBlogGrande">
				<?php
				//stampo i blog che hanno nel titolo ciò che l'utente ha cercato
				while ($rowCercaBLOG = $resCercaBLOG -> fetch_assoc()) { 
					$idBlog = $rowCercaBLOG["idblog"];
					//seleziono la foto del blog
					$queryFotoBlog = "SELECT file FROM FotoBlog, multimedia WHERE idmultimedia = idFoto AND idBlog = '$idBlog' AND sfondo = '0'";
					$resFotoBlog = $mysqli -> query($queryFotoBlog);
					$rowFotoBlog = $resFotoBlog -> fetch_assoc(); 
					?>
					<article class="contenitoreBlog">
						<p class="titoloBlog"><a href="blog.php?id=<?php echo $rowCercaBLOG["idblog"]?>"><?php echo $rowCercaBLOG["titolo"]?></a></p>
						<?php 
						//mostro la foto del blog
						if ($resFotoBlog -> num_rows != 0) { ?>
							<p><img src="uploads/<?php echo $rowFotoBlog["file"]?>" class="immagineDelBlog"></p>
						<?php } else { ?>
							<p><img src="uploads/default.jpeg" class="immagineDelBlog"></p>
						<?php } 
						if (isset($_SESSION["idutente"])) {
							//query che seleziona gli id dei blog che l'utente segue
							$queryBClick = "SELECT idblog FROM segueblog WHERE idutente = '$idutente' AND idblog='$idBlog'";
							$resBClick = $mysqli -> query($queryBClick);
							//se l'utente è premium o segue meno di 5 blog mostro il tasto segui o non segui
							if ($rowQueryBlogTipo["tipo"] == "premium" || $rowQueryBlogTipo["QuantiBlog"] < 5) {
								if($resBClick -> num_rows == 0){ ?>
									<input type="submit" name="" class="Segui" id="<?php echo $idBlog?>" value="segui">
								<?php } else { ?>
									<input type="submit" name="" class="SeguiNON" id="<?php echo $idBlog?>"value="non seguire">
								<?php }
								//se l'utente è standard e segue meno di 5 blog mostro il tasto non seguire per i blog che segue
							} elseif ($rowQueryBlogTipo["tipo"] == "standard" && $rowQueryBlogTipo["QuantiBlog"] >= 5) {
								if (isset($_SESSION["idutente"])) {
									if($resBClick -> num_rows != 0){ ?>
										<input type="submit" name="" class="SeguiNON" id="<?php echo $idBlog?>"value="non seguire">
									<?php }
								}
							}
						}?>
					</article>
				<?php } ?>
			</div>
			<?php
			if ($resCercaPOST -> num_rows != 0) { ?>
				<h1 id="MieiBlog">Post</h1>
			<?php } 
			//stampo i post che hanno nel titolo o nel testo ciò che l'utente ha cercato
			while ($rowCercaPOST = $resCercaPOST -> fetch_assoc()) { 
				$IDpost = $rowCercaPOST["idpost"];
				if (isset($_SESSION["idutente"])) {
					//guardo se l'utente ha messo mi piace a un post
					$queryCheckLike = "SELECT * FROM piaciuti WHERE idutente = '$idutente' AND idpost = '$IDpost'";
					$resCheckLike = $mysqli -> query($queryCheckLike);
				}
				//guardo quanti like ha un post
				$sqlNumeroLike = "SELECT count(*) as mipiace, idpost FROM piaciuti WHERE idpost = '$IDpost'";
				$resNumeroLike = $mysqli -> query($sqlNumeroLike);
				$rowNumeroLike = $resNumeroLike -> fetch_assoc();
				//prendo la foto del post
				$queryFotoPost = "SELECT file FROM multimedia, foto WHERE idmultimedia = foto.idfoto AND foto.idpost = '$IDpost'";
				$resFotoPost = $mysqli -> query($queryFotoPost);
				$rowFotoPost = $resFotoPost -> fetch_assoc();
				?>
				<article class="Posts">
					<?php 
					if($resFotoPost -> num_rows != 0){ ?>
						<p><img src="uploads/<?php echo $rowFotoPost["file"]?>" class="immaginiDeiPost"></p>
					<?php } ?>
					<p class="titoloPost"><a href="blog.php?id=<?php echo $rowCercaPOST["idblog"]?>"><?php echo $rowCercaPOST["titolo"]?></a></p>
					<p class="testoPost"><?php echo $rowCercaPOST["testo"]?></p>
					<?php
					if (isset($_SESSION["idutente"])) {
						//se l'utente loggato è l'autore del post lo può eliminare
						if ($idutente == $rowCercaPOST["idautore"]) { ?>
							<p class="eliminaPost" id="<?php echo $rowCercaPOST["idpost"]?>"><i class="glyphicon glyphicon-trash" alt="Elimina post" title="Elimina post"></i></p>
						<?php } 
						//se l'utente loggato non ha messo like al post allora lo può mettere, altrimenti lo può togliere
						if ($resCheckLike -> num_rows == 0){ ?>
							<img src="heart.png" name="piaciuto" class="piaciuto" id="<?php echo $rowCercaPOST["idpost"]?>" alt="like" title="Like">
						<?php } else { ?>
							<img src="like.png" name="NONpiaciuto" class="NONpiaciuto" id="<?php echo $rowCercaPOST["idpost"]?>" alt="togli like" title="Togli il Like">
						<?php } 
					}
					?>
					<!--numero dei like del post-->
					<p class="numeroLike"><i class="glyphicon glyphicon-heart"></i><?php echo $rowNumeroLike["mipiace"]?></p>
					<?php
					//se l'utente è loggato può commentare
					if (isset($_SESSION["idutente"])) { ?>
						<form method="post">
							<input type="text" class="InserisciCommento" placeholder="commento">
							<input type="button" name="btn-commenta" class="btn-commenta" id="<?php echo $rowCercaPOST["idpost"]?>" value="invio">
						</form>
					<?php }
					//query che prende i commenti e il nome del commentatore
					$queryStampaCommenti = "SELECT *, nickname FROM commenti, utente WHERE idpost = '$IDpost' AND idutente = idautore";
					$resStampaCommenti = $mysqli -> query($queryStampaCommenti);
					//mostro i commenti e l'autore del commento
					while ($rowStampaCommenti = $resStampaCommenti -> fetch_assoc()) { ?>
						<a href="profilo.php?id=<?php echo $rowStampaCommenti["idautore"]?>" class="stampaCommenti"><?php echo $rowStampaCommenti["nickname"]?>
						</a>
						<p id="<?php echo $rowStampaCommenti["idcommento"]?>" class="stampaCommentiTesto"><?php echo $rowStampaCommenti["testo"]?></p>
						<?php 
						//se l'utente loggato è l'autore del comment lo può eliminare
						if(isset($_SESSION["idutente"])){
							if ($idutente == $rowStampaCommenti["idautore"]) { ?>
								<p class="eliminaCommento" id="<?php echo $rowStampaCommenti["idcommento"]?>"><i class="glyphicon glyphicon-trash" style="cursor: pointer;" title="elimina commento"></i></p>
							<?php } 
						} 
					} ?>
				</article>
			<?php } 
		} 
		//cerco all'interno del blog
		if (isset($_GET["idCercaBlog"], $_POST["searchBlog"])) { 
			while ($rowCercaInBlog = $resCercaInBlog -> fetch_assoc()) { 
				$IDpost = $rowCercaInBlog["idpost"];
				if (isset($_SESSION["idutente"])) {
					//guardo se l'utente ha messo like a un post
					$queryCheckLike = "SELECT * FROM piaciuti WHERE idutente = '$idutente' AND idpost = '$IDpost'";
					$resCheckLike = $mysqli -> query($queryCheckLike);
				}
				//guardo quanti like ha un post
				$sqlNumeroLike = "SELECT count(*) as mipiace, idpost FROM piaciuti WHERE idpost = '$IDpost'";
				$resNumeroLike = $mysqli -> query($sqlNumeroLike);
				$rowNumeroLike = $resNumeroLike -> fetch_assoc();
				//guardo se un post ha la foto
				$queryFotoPost = "SELECT file FROM multimedia, foto WHERE idmultimedia = foto.idfoto AND foto.idpost = '$IDpost'";
				$resFotoPost = $mysqli -> query($queryFotoPost);
				$rowFotoPost = $resFotoPost -> fetch_assoc();
				?>
				<article class="Posts">
					<?php 
					if ($resFotoPost -> num_rows != 0) { ?>
						<p><img src="uploads/<?php echo $rowFotoPost["file"]?>" class="immaginiDeiPost"></p>
					<?php } ?>
					<p class="testoPost"><?php echo $rowCercaInBlog["titolo"]?></p>
					<p class="AutorePost"><?php echo $rowCercaInBlog["testo"]?></p>
					<?php 
					if (isset($_SESSION["idutente"])) {
						//se l'utente loggato è l'autore del post lo può eliminare
						if ($idutente == $rowCercaInBlog["idautore"]) { ?>
							<p class="eliminaPost" id="<?php echo $rowCercaInBlog["idpost"]?>"><i class="glyphicon glyphicon-trash" title="elimina"></i></p>
						<?php } 
						//se l'utente loggato non ha messo like al post allora lo può mettere, altrimenti lo può togliere
						if ($resCheckLike -> num_rows == 0){ ?>
							<img src="heart.png" name="piaciuto" class="piaciuto" id="<?php echo $rowCercaInBlog["idpost"]?>" >
						<?php } else { ?>
							<img src="like.png" name="NONpiaciuto" class="NONpiaciuto" id="<?php echo $rowCercaInBlog["idpost"]?>">
						<?php } 
					}
					?>
					<!--numero dei like del post-->
					<p class="numeroLike"><i class="glyphicon glyphicon-heart"></i><?php echo $rowNumeroLike["mipiace"]?></p>
					<?php
					//se l'utente è loggato può commentare
					if (isset($_SESSION["idutente"])) { ?>
						<form method="post">
							<input type="text" class="InserisciCommento" placeholder="commento" style="color: black">
							<input type="button" name="btn-commenta" class="btn-commenta" id="<?php echo $rowCercaInBlog["idpost"]?>" value="invio">
						</form>
					<?php }
					//query che stampa i commenti e il nome del commentatore
					$queryStampaCommenti = "SELECT *, nickname FROM commenti, utente WHERE idpost = '$IDpost' AND idutente = idautore";
					$resStampaCommenti = $mysqli -> query($queryStampaCommenti);
					while ($rowStampaCommenti = $resStampaCommenti -> fetch_assoc()) { ?>
						<a href="profilo.php?id=<?php echo $rowStampaCommenti["idautore"]?>" class="stampaCommenti"><?php echo $rowStampaCommenti["nickname"]?>
						</a>
						<p id="<?php echo $rowStampaCommenti["idcommento"]?>" class="stampaCommentiTesto"><?php echo $rowStampaCommenti["testo"]?></p>
						<?php 
						//se l'utente loggato è l'autore del commento lo può eliminare
						if(isset($_SESSION["idutente"])){
							if ($idutente == $rowStampaCommenti["idautore"]) { ?>
								<p class="eliminaCommento" id="<?php echo $rowStampaCommenti["idcommento"]?>"><i class="glyphicon glyphicon-trash" title="elimina commento"></i></p>
							<?php } 
						} 
					} ?>
				</article>
			<?php }
		} 
		//cerco blog all'interno del tema
		if (isset($_GET["idCercaTema"], $_POST["searchTema"])) { ?>
			<div class="contenitoreBlogGrande">
				<?php
				while ($rowBlogTema = $resBlogTema -> fetch_assoc()) {  
					//guardo se l'utente segue il blog
					$idBlog = $rowBlogTema["idblog"];
					//query che seleziona la foto profilo del blog
					$queryFotoBlog = "SELECT file FROM FotoBlog, multimedia WHERE idmultimedia = idFoto AND idBlog = '$idBlog' AND sfondo = '0'";
					$resFotoBlog = $mysqli -> query($queryFotoBlog);
					$rowFotoBlog = $resFotoBlog -> fetch_assoc(); 
					?>
					<!--mostro foto, tema e autore dei blog-->
					<article class="contenitoreBlog">
						<?php
						if ($resFotoBlog -> num_rows != 0) { ?>
							<p><img src="uploads/<?php echo $rowFotoBlog["file"]?>" class="immagineDelBlog"></p>
						<?php } else { ?>
							<p><img src="uploads/default.jpeg" class="immagineDelBlog"></p>
						<?php } ?>
						<p class="titoloBlog"><a href="blog.php?id=<?php echo $rowBlogTema["idblog"]?>"><?php echo $rowBlogTema["titolo"]?></a></p>
						<p class="autoreBlog"><a href="profilo.php?id=<?php echo $rowBlogTema["autore"]?>"><?php echo $rowBlogTema["nickname"]?></a></p>
						<?php 
						if (isset($_SESSION["idutente"])) {
							//query che guarda se un utente segue il blog
							$queryBClick = "SELECT idblog FROM segueblog WHERE idutente = '$idutente' AND idblog='$idBlog'";
							$resBClick = $mysqli -> query($queryBClick);
							//se l'utente è premium o segue meno di 5 blog può seguire o smettere di seguire il blog
							if ($rowQueryBlogTipo["tipo"] == "premium" || $rowQueryBlogTipo["QuantiBlog"] < 5) {
								if($resBClick -> num_rows == 0){ ?>
									<input type="submit" name="" class="Segui" id="<?php echo $idBlog?>" value="segui">
								<?php } else { ?>
									<input type="submit" name="" class="SeguiNON" id="<?php echo $idBlog?>"value="non seguire">
								<?php }
								//se l'autore è standard e segue almeno 5 blog può smettere di seguire il blog
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
		<?php } 
		//se non vengono trovati risultati 
		if(isset($_GET["idCerca"], $_POST["searchPr"]) && $resCercaPOST -> num_rows == 0 && $resCercaBLOG -> num_rows == 0){ ?>
			<article class="risultatoRicerca">
				<h1 id="MieiBlog">Non sono stati trovati risultati</h1>
			</article> 
		<?php } 
		if(isset($_GET["idCercaBlog"], $_POST["searchBlog"]) && $resCercaInBlog -> num_rows == 0){ ?>
			<article class="risultatoRicerca">
				<h1 id="MieiBlog">Non sono stati trovati risultati</h1>
			</article> 
		<?php } 
			if(isset($_GET["idCercaTema"], $_POST["searchTema"]) && $resBlogTema -> num_rows == 0){ ?>
			<article class="risultatoRicerca">
				<h1 id="MieiBlog">Non sono stati trovati risultati</h1>
			</article> 
		<?php } ?> 
		<script type="text/javascript">
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
				$("#cerca").keyup(function(){
					if ($("#cerca").val() != "") {
						$("#usercheck").css("display", "block");
					}else{
						$("#usercheck").css("display", "none");
					}
				});
				<?php 
				if (isset($_SESSION["idutente"])) { ?>
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
					//chiamata ajax per smettere di seguire un blog
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
					//chiamata ajax per mettere mi piace a un post
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
				 	//chiamata ajax per togliere il like a un post
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
				 	//chiamata ajax per commentare un post
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
				 	//chiamata ajax per eliminare un post
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
				<?php } ?>
			});
		</script>
	<?php } else { ?>
		<!--l'utente inserisce caratteri non accettati-->
		<h1 id="MieiBlog">QUALCOSA È ANDATO STORTO...</h1>
		<img src="uploads/404error.png" id="errImg">
		<script type="text/javascript">
			$(document).ready(function(){
				$("#cerca").keyup(function(){
					if ($("#cerca").val() != "") {
						$("#usercheck").css("display", "block");
					}else{
						$("#usercheck").css("display", "none");
					}
				});
				//chiamata ajax per cercare il nome nel database
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
	<?php } ?>
</body>
</html>