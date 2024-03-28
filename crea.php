<?php
session_start();
//mi connetto al database
include('connetti.php');
if (isset($_SESSION["idutente"])) {
	$idutente = $_SESSION["idutente"];
	//controllo se quanti blog ha creato l'utente e il tipo dell'utente
	$query = "SELECT count(*) as Quanti from blog, utente WHERE autore = idutente AND idutente = '$idutente'";
	$res = $mysqli -> query($query);
	$row = $res -> fetch_assoc();
	//seleziono il tipo dell'utente loggato
	$queryTipoUt = "SELECT tipo FROM utente WHERE idutente = '$idutente'";
	$resTipoUt = $mysqli -> query($queryTipoUt);
	$rowTipo = $resTipoUt -> fetch_assoc();
}
if (isset($_SESSION["idBlog"])) {
	unset($_SESSION["idBlog"]);
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Crea un Blog</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="Progetto_Basi.css">
	<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
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
			<a href="home2.php?logout"><i class="glyphicon glyphicon-cog"></i> LogOut</a>
		<?php } else { ?>
			<a class="active" href="home2.php"><i class="glyphicon glyphicon-home"></i> Home</a>
			<a href="login.php"><i class="glyphicon glyphicon-cog"></i> LogIn/Registrati</a>
		<?php } ?>
	</div>
	<span id="usercheck"></span>
	<article id="bacheca">
		<?php
		if (isset($_SESSION["idutente"])) { ?>
			<!--form per la creazione del blog-->
			<form action="checkCrea.php" method="post" class="creaBlog" enctype="multipart/form-data"  >
				<h1 id="MieiBlog">Crea il tuo blog!</h1>
				<input type="text" id="nomeBlog" name="nomeBlog" placeholder="Nome del blog" class="w3-input" maxlength="40"><br>
				<span id="checkNomeBlog"></span>
				<input type="text" id="temaBlog" name="temaBlog" placeholder="Tema del blog" class="w3-input" maxlength="20"><br>
				<label>Aggiungi immagine di profilo del blog</label>
				<input type="file"  name="file">
				<div id="suggestion-box"></div>
				<?php 
				//se l'utente è premium può inserire un'immagine di sfondo e un font 
				if($rowTipo["tipo"] == "premium"){ ?>
				<label>Aggiungi immagine di sfondo</label>
					<input type="file"  name="fileSfondo">
					<label>Scegli il font</label>
					<select id="scegliFont" name="font">
						<p id="font1"><option value="1">Baskerville</option></p>
						<p id="font2"><option value="2">Architets Daughter</option></p>
						<p id="font3"><option value="3">Gochi Hand</option></p>
						<p id="font4"><option value="4">Pacifico</option></p>
						<p id="font5"><option value="5">Orbitron</option></p>
					</select><br><br><br>
				<?php }
				if($rowTipo["tipo"] == "premium" || $row["Quanti"] < 5){ ?>
					<input type="submit"  value="Crea" class="btn-crea" ><br>
				<?php } 
				//se l'utente è standard e ha almeno 5 blog non mostro il pulsante per creare il blog
				elseif ($row["tipo"] == "standard" && $row["Quanti"] >= 5) { ?>
					<p id="MaxBlog">Hai raggiunto il limite massimo di blog creati. Per creare blog senza limiti diventa <button><a href="modifica.php">premium</a></button></p>
				<?php } ?>
			</form>
		<?php } elseif(!isset($_SESSION["idutente"])) { ?>
			<h1 id="MieiBlog">Puoi creare un blog solo dopo aver effettuato il login</h1>
		<?php } ?>
		<div id="ErroreLog">
			<?php
			//mostro il messaggio di errore se il blog non è creato correttamente
			if (isset($_SESSION["msgCrea"])) {
				echo $_SESSION["msgCrea"];
				unset($_SESSION["msgCrea"]);
			}
			?>
		</div>
	</article>
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
		//chiamata ajax per mostrare i temi con i caratteri inseriti
		$("#temaBlog").keyup(function(){
			$.ajax({
				type: "POST",
				url: "checkTema.php",
				data: {
					keyword: $(this).val()
				},
				success: function(data){
					$("#suggestion-box").html(data);
				}
			});
		});
		//chiamata ajax per verificare che il nome del blog non sia già usato
		$("#nomeBlog").keyup(function(){
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
	});
	</script>
</body>
</html>