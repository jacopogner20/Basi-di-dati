<?php
session_start();
//mi connetto al database
include('connetti.php');
if (isset($_SESSION["idutente"])) {
	$idutente = $_SESSION["idutente"];
}
if(isset($_SESSION["username"])){
	$user = $_SESSION["username"];	
	//prendo tutti i dati dell'utente
	$queryAggiorna = "SELECT * FROM utente WHERE idutente = '$idutente'";
	$resAggiorna = $mysqli -> query($queryAggiorna);
	$rowAggiorna = $resAggiorna -> fetch_assoc();
}
if(isset($_SESSION["idBlog"])){
	$idBlog = $_SESSION["idBlog"];
	//prendo i dati del blog
	$queryAggiornaBlog = "SELECT * FROM blog WHERE idblog = '$idBlog'";
	$resAggiornaBlog = $mysqli -> query($queryAggiornaBlog);
	$rowAggiornaBlog = $resAggiornaBlog -> fetch_assoc();
	//query che prende gli id dei coautori del blog
	$queryCoautori = "SELECT nickname, idutente FROM utente, coautore WHERE idutente = idcoautore AND idblog = '$idBlog'";
	$resCoautori = $mysqli -> query($queryCoautori);
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Impostazioni</title>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="Progetto_Basi.css">
	<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
</head>
<body class="container">
	<div class="topnav">
		<!--form per cercare all'interno del sito-->
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
	//se è settata la sessione del blog mostro i dati del blog
	if(isset($_SESSION["idBlog"])){  ?>
		<h1 id="MieiBlog">Lista coautori:</h1>
		<?php 
		if($resCoautori -> num_rows > 0){
			//stampo i nomi dei coautori
			while ($rowCoautori = $resCoautori -> fetch_assoc()) { ?>
				<a href="profilo.php?id=<?php echo $rowCoautori["idutente"]?>"><?php echo $rowCoautori["nickname"]; ?></a>
			<?php } 
		} else { ?>
			<p>Questo blog non ha coautori</p>
		<?php } ?>
		<div id="contenitoreModifiche">
			<h1>Modifica i dati del tuo blog</h1>
			<!--form per modificare i dati del blog-->
			<form method="post" action="checkModifica.php?idBlog=<?php echo $idBlog ?>" id="modificaForm" enctype="multipart/form-data">
				<label for="titolo">Titolo Blog:</label>
				<input type="text" name="titolo" id="titoloBlog" value="<?php echo $rowAggiornaBlog["titolo"]?>" class="w3-input" maxlength="40"><br>
				<span id="checkNomeBlog"></span>
				<!--campo per aggiungere il coautore-->
				<label>Aggiungi coautore</label>
				<input type="text" name="coautore" id="coautore" class="w3-input" maxlength="15"><br>
				<?php
				//se ci sono coautori li posso eliminare
				if($resCoautori -> num_rows != 0){ ?>
					<label>Elimina coautore</label>
					<input type="text" name="EliminaCoautore" id="EliminaCoautore" class="w3-input" maxlength="15"><br>
				<?php } ?>
				<!--cambiare foto profilo del blog-->
				<label>Aggiorna immagine Blog</label>
				<input type="file"  name="file">
				<?php 
				//se l'utente è premium può cambiare il font e l'immagine di sfondo del blog
				if ($rowAggiorna["tipo"] == "premium") { ?>
					<label>Aggiungi immagine di sfondo</label>
					<input type="file"  name="fileSfondo">
					<label>Scegli il font</label>
					<div id="fontModifica">
						<select id="scegliFont" name="font">
							<option id="font1" value="1">Baskerville</option>
							<option id="font2" value="2">Architets Daughter</option>
							<option id="font3" value="3">Gochi Hand</option>
							<option id="font4" value="4">Pacifico</option>
							<option id="font5" value="5">Orbitron</option>
						</select>
					</div>
				<?php } ?>
				<input type="submit" name="modifica" id="modifica" value="Salva le modifiche" class="w3-input">
			</form>
			<!--pulsante per eliminare il blog-->
			<input type="button" name="btn-cancella" id="btn-cancella" value="Elimina blog" class="w3-input">
			<div style="display: none;" id="ConfermaEliminazione">
				<h2 id="MieiBlog">Vuoi davvero eliminare il blog?</h2>
				<button id="annulla" class="w3-input">Annulla</button>
				<button id="confermaBl" class="w3-input"><a href="profilo.php?id=<?php echo $idutente ?>">Conferma</a></button></div>
		</div>
		<!--se non è settata la sessione del blog mostro i dati dell'utente-->
	<?php } elseif(isset($_SESSION["idutente"])) { ?>
		<div id="contenitoreModifiche">
			<!--form per modificare i dati dell'utente-->
			<h1 id="MieiBlog">Modifica i tuoi dati</h1>
			<form method="post" action="checkModifica.php" id="modificaForm" enctype="multipart/form-data">
				<label for="nome">Nome:</label>
				<input type="text" name="nome" id="nomeUtente"  class="w3-input" value="<?php echo $rowAggiorna["nome"]?>" maxlength="20"><br>
				<label for="cognome">Cognome:</label>
				<input type="text" name="cognome" id="cognomeUtente" value="<?php echo $rowAggiorna["cognome"]?>" class="w3-input" maxlength="20"><br>
				<label for="nickname">Nickname:</label>
				<input type="text" name="nickname" id="nicknameUtente" value="<?php echo $rowAggiorna["nickname"]?>" class="w3-input" maxlength="15"><br>
				<span id="checkNickname"></span>
				<label for="email">Email:</label>
				<input type="text" name="email" id="emailUtente" value="<?php echo $rowAggiorna["mail"]?>" class="w3-input" maxlength="25"><br>
				<span id="checkEmail"></span>
				<label for="documento">Documento:</label>
				<input type="text" name="documento" id="documentoUtente" value="<?php echo $rowAggiorna["documento"]?>" class="w3-input"maxlength="15"><br>
				<span id="checkDocumento"></span>
				<label for="telefono">Telefono:</label>
				<input type="text" name="telefono" id="telefonoUtente" value="<?php echo $rowAggiorna["telefono"]?>" class="w3-input" maxlength="10"><br>
				<label for="bio">Bio:</label>
				<input type="textarea" name="Bio" id="BioUtente" value="<?php echo $rowAggiorna["bio"]?>" class="w3-input"><br>
				<label for="password">Password:</label>
				<input type="text" name="password" id="password" value="<?php echo $rowAggiorna["password"]?>" class="w3-input" maxlength="15"><br>
				<!--aggiornare immagine del profilo-->
				<label>Aggiorna immagine Profilo</label>
				<input type="file"  name="file">
				<?php 
				//se l'utente è standard mostro i campi della carta di credito per diventare premium
				if ($rowAggiorna["tipo"] == "standard") { ?>
					<div id="VantaggiPremium">
						<h3>diventa premium perché....</h3>
						<ul>
							<li>crea infiniti blog</li>
							<li>personalizzazione dei blog</li>
							<li>segui utenti, temi e blog senza alcun limite</li>
						</ul>
						<h3>Solo 2,99€ al mese</h3>
					</div>
					<!--form per i dati della carta-->
					<div id="contenitorePremium">
						<label for="carta">Numero carta:</label>
						<input type="text" class="w3-input" name= "carta" id="numeroCarta" placeholder="XXXX-XXXX-XXXX-XXXX" maxlength="16"><br>
						<label for="scadenza">Data scadenza:</label>
						<select name="mese">
							<option value="1">1</option>
							<option value="2">2</option>
							<option value="3">3</option>
							<option value="4">4</option>
							<option value="5">5</option>
							<option value="6">6</option>
							<option value="7">7</option>
							<option value="8">8</option>
							<option value="9">9</option>
							<option value="10">10</option>
							<option value="11">11</option>
							<option value="12">12</option>
						</select>
						<label>/</label>
						<select name="anno">
							<option value="1">2020</option>
							<option value="2">2021</option>
							<option value="3">2022</option>
							<option value="4">2023</option>
							<option value="5">2024</option>
							<option value="6">2025</option>
							<option value="7">2026</option>
							<option value="8">2027</option>
							<option value="9">2028</option>
						</select><br>
						<label for="CVC/CVV">CVC/CVV:</label>
						<input type="text" class="w3-input" name="CVV" id="codiceSegreto" maxlength="3" placeholder="123"><br>
					</div>	
				<?php } ?>
				<input type="submit" name="modifica" id="modifica" value="Salva le modifiche" class="w3-input">
			</form>
			<?php 
			//se l'utente è premium mostro il pulsante per disdire l'abbonamento
			if ($rowAggiorna["tipo"] == "premium") { ?>
				<input type="button" name="btn-disdici" id="btn-disdici" value="Disdici abbonamento" class="w3-input" >
				<div style="display: none;" id="ConfermaDisdici" >
					<h2 id="MieiBlog">Vuoi davvero disdire l'abbonamento?</h2>
					<button class="w3-input" id="annullaDisdici">Annulla</button>
					<button class="w3-input" id ="DisdiciAbb"><a href="profilo.php?id=<?php echo $idutente?>">Conferma</a></button></div>
			<?php } ?>
			<!--mostro i pulsanti per eliminare l'utente-->
			<input type="button" name="btn-cancella" id="btn-cancella" value="Elimina utente" class="w3-input" >
			<div style="display: none;" id="ConfermaEliminazione">
				<h2 id="MieiBlog">Vuoi davvero eliminare l'utente? I tuoi blog, post e commenti saranno eliminati definitivamente.</h2>
				<button class="w3-input" id="annulla">Annulla</button>
				<button class="w3-input" id ="confermaUt"><a href="home2.php?logout">Elimina</a></button>
			</div>
		</div>
	<?php } 
	if(!isset($_SESSION["idutente"]) && !isset($_SESSION["idBlog"])){ ?>
		<body class="container">
		<div class="topnav">
			<!--form per cercare all'interno del sito-->
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
		<article id="bacheca">
			<h1 id="MieiBlog">Per modificare i tuoi dati devi effettuare il login</h1>
		</article>
		<script type="text/javascript">
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
		</script>
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
			<?php  
			//modifico l'opzione del font che mostro
			if (isset($rowAggiornaBlog["font"])) { ?>
				var fontBlog = <?php echo $rowAggiornaBlog["font"]?>;
				$("#fontModifica select").val(fontBlog);
			<?php } ?>
			$("#btn-cancella").click(function(){
				$("#ConfermaEliminazione").css("display", "block");
				$(this).hide();
			});
			$("#annulla").click(function(){
				$("#ConfermaEliminazione").css("display", "none");
				$("#btn-cancella").show();
			});
			$("#btn-disdici").click(function(){
				$("#ConfermaDisdici").css("display", "block");
				$(this).hide();
			});
			$("#annullaDisdici").click(function(){
				$("#ConfermaDisdici").css("display", "none");
				$("#btn-disdici").show();
			});
			<?php
			// chiamata ajax per eliminare il blog 
			if (isset($_SESSION["idBlog"])) { ?>
				$("#confermaBl").click(function(){ 
					$.ajax({
						type: "POST",
						url: "checkModifica.php",
						dataType: "html",
						data:{
							idBlogElimina: <?php echo $idBlog ?>,
						}
					});
				});
				//chiamata ajax per controllare che il titolo del blog sia disponibile
				$("#titoloBlog").keyup(function(){
					var nomeBlogCheck = $(this).val();
					$.post(
						"CheckRegistrazione.php",
						{
							nomeBlogCheck: nomeBlogCheck
						},
						function(data){
							$("#checkNomeBlog").html(data);
						}
					);
				});
			<?php } 
			//chiamata ajax per eliminare l'utente
			if (isset($_SESSION["idutente"])) { ?>
				$("#confermaUt").click(function(){
					$.ajax({
						type: "POST",
						url: "checkModifica.php",
						dataType: "html",
						data:{
							idUtenteElimina: <?php echo $idutente ?>,
						}
					});
				});
				//chiamata ajax per disdire l'abbonamento
				$("#DisdiciAbb").click(function(){
					$.ajax({
						type: "POST",
						url: "checkModifica.php",
						dataType: "html",
						data:{
							disdici: "standard"
						}
					});
				});
				//chiamata ajax per controllare che il nickname utente sia valido
				$("#nicknameUtente").keyup(function(){
				var nickname = $(this).val();
				$.post(
					"CheckRegistrazione.php",
					{
						nickname: nickname
					},
					function(data){
						$("#checkNickname").html(data);
					}
				);
				});
				//chiamata ajax per controllare che la mail sia valida
				$("#emailUtente").keyup(function(){
				var mail = $(this).val();
				$.post(
					"CheckRegistrazione.php",
					{
						mail: mail
					},
					function(data){
						$("#checkEmail").html(data);
					}
				);
				});
				//chiamata ajax per controllare che il documento sia valido
				$("#documentoUtente").keyup(function(){
				var documento = $(this).val();
				$.post(
					"CheckRegistrazione.php",
					{
						documento: documento
					},
					function(data){
						$("#checkDocumento").html(data);
					}
				);
				});
			<?php } ?>
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
</body>
</html>