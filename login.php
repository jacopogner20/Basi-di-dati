<?php 
session_start();
?>
<!DOCTYPE html>
<html>
<head>
	<title>Accedi o iscriviti</title>
	<meta charset="utf-8">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="Progetto_Basi.css">
	<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
</head>
<body class="container">
	<div id="contenitoreFormLogin">
		<!--form per i dati di login-->
		<form method="post" action="checkLogin.php" id="loginForm">
			<h1>Accedi</h1>
			<input type="text" name="email" id="email" placeholder="Email" maxlength="25">
			<input type="password" name="passwordLog" id="passwordLog" placeholder="Password" maxlength="15">
			<input type="submit" name="accedi"  class="btn-login" id="buttonAccedi" value="accedi">
		</form>
		<div id="ErroreLog">
			<!--errore di login-->
			<?php 
			if (isset($_SESSION['messaggio'])) {
				echo $_SESSION['messaggio'];
				unset($_SESSION['messaggio']);
			}
			?>
		</div>
	</div>
	<hr>
	<div>
		<h1>Crea un account</h1>
		<div id="ErroreLog">
			<!--errore di registrazione-->
			<?php
			if (isset($_SESSION["ErrRegistrazione"])) {
				echo $_SESSION["ErrRegistrazione"];
				unset($_SESSION["ErrRegistrazione"]);
			}
			?>
		</div>
	</div>
	<!--form per i dati di registrazione-->
	<div id="contenitoreFormRegistrazione">
		<form method="post" action="registrazione1.php"  enctype="multipart/form-data" id="registrazioneForm">
			<label for="nome">Nome:</label>
			<?php 
			if (isset($_SESSION["nome"])) {
				$nomeS = $_SESSION["nome"]; ?>
				<input type="text" class="w3-input" name="nome" id="nomeUtente" maxlength="20" value="<?php echo $nomeS ?>"><br>
			<?php } else { ?>
				<input type="text" class="w3-input" name="nome" id="nomeUtente" maxlength="20" ><br>
			<?php } ?>
			<div id="ErroreLog">
				<?php
				//messaggio di errore per il nome
				if (isset($_SESSION["ErroreNome"])) {
					echo $_SESSION["ErroreNome"];
					?>
					<br>
					<br>
					<?php
					unset($_SESSION["ErroreNome"]);
				}
				?>
			</div>
			<label for="cognome">Cognome:</label>
			<?php
			if (isset($_SESSION["cognome"])) {
				$cognomeS = $_SESSION["cognome"]; ?>
				<input type="text" class="w3-input" name="cognome" id="cognomeUtente" maxlength="20" value="<?php echo $cognomeS ?>"><br>
			<?php } else { ?>
				<input type="text" class="w3-input" name="cognome" id="nomeUtente" maxlength="20"><br>
			<?php } ?>
			<div id="ErroreLog">
				<?php
				//messaggio di errore per il cognome
				if (isset($_SESSION["ErroreCognome"])) {
					echo $_SESSION["ErroreCognome"];
					?>
					<br>
					<br>
					<?php
					unset($_SESSION["ErroreCognome"]);
				}
				?>
			</div>
			<label for="nickname">Nickname:</label>
			<?php
			if (isset($_SESSION["nickname"])) {
				$nicknameS = $_SESSION["nickname"]; ?>
				<input type="text" class="w3-input" name="nickname" id="nicknameUtente" value="<?php echo $nicknameS ?>" maxlength="20"><br>
			<?php } else { ?>
				<input type="text" class="w3-input" name="nickname" id="nicknameUtente" maxlength="20"><br>
			<?php } ?>
			<span id="checkNickname" maxlength="20"></span>
			<div id="ErroreLog">
				<?php 
				//messaggio di errore per il nickname
				if (isset($_SESSION["ErroreNickname"])) {
					echo $_SESSION["ErroreNickname"];
					?>
					<br>
					<br>
					<?php
					unset($_SESSION["ErroreNickname"]);
				}
				?>
			</div>
			<label for="email">Email:</label>
			<?php
			if (isset($_SESSION["email"])) {
				$mailS = $_SESSION["email"]; ?>
				<input type="text" class="w3-input" name="email" id="emailUtente" value="<?php echo $mailS ?>" maxlength="25"><br>
			<?php } else { ?>
				<input type="text" class="w3-input" name="email" id="emailUtente" maxlength="25"><br>
			<?php } ?>
			<span id="checkEmail"></span>
			<div id="ErroreLog">
				<?php 
				//messaggio di errore per la mail
				if (isset($_SESSION["ErroreMail"])) {
					echo $_SESSION["ErroreMail"];
					?>
					<br>
					<br>
					<?php
					unset($_SESSION["ErroreMail"]);
				}
				?>
			</div>
			<label for="documento">Documento:</label>
			<?php
			if (isset($_SESSION["documento"])) {
				$documentoS = $_SESSION["documento"]; ?>
				<input type="text" class="w3-input" name="documento" id="documentoUtente" value="<?php echo $documentoS ?>" maxlength="15"><br>
			<?php } else { ?>
				<input type="text" class="w3-input" name="documento" id="documentoUtente" maxlength="15"><br>
			<?php } ?>
			<span id="checkDocumento"></span>
			<div id="ErroreLog">
				<?php 
				//messaggio di errore per il documento
				if (isset($_SESSION["ErroreDocumento"])) {
					echo $_SESSION["ErroreDocumento"];
					?>
					<br>
					<br>
					<?php
					unset($_SESSION["ErroreDocumento"]);
				}
				?>
			</div>
			<label for="telefono">Telefono:</label>
			<?php 
			if (isset($_SESSION["telefono"])) {
				$telefonoS = $_SESSION["telefono"]; ?>
				<input type="text" class="w3-input" name="telefono" id="telefonoUtente" value="<?php echo $telefonoS ?>" maxlength="10"><br>
			<?php } else { ?>
				<input type="text" class="w3-input" name="telefono" id="telefonoUtente" maxlength="10"><br>
			<?php } ?>
			<div id="ErroreLog">
				<?php
				//messaggio di errore per il telefono
				if (isset($_SESSION["ErroreTelefono"])) {
					echo $_SESSION["ErroreTelefono"];
					?>
					<br>
					<br>
					<?php
					unset($_SESSION["ErroreTelefono"]);
				}
				?>
			</div>
			<label for="bio">Bio:</label>
			<?php
			if (isset($_SESSION["Bio"])) {
				$BioS = $_SESSION["Bio"]; ?>
				<input type="text" class="w3-input" name="Bio" id="BioUtente" value="<?php echo $BioS ?>"><br>
			<?php } else { ?>
				<input type="text" class="w3-input" name="Bio" id="BioUtente"><br>
			<?php } ?>
			<div id="ErroreLog">
				<?php
				//messaggio di errore per la Bio
				if (isset($_SESSION["ErroreBio"])) {
					echo $_SESSION["ErroreBio"];
					?>
					<br>
					<br>
					<?php
					unset($_SESSION["ErroreBio"]);
				}
				?>
			</div>
			<label for="password">Password:</label>
			<input type="password" class="w3-input" name="password" id="password" maxlength="15"><br>
			<div id="ErroreLog">
				<?php 
				//messaggio di errore per la password
				if (isset($_SESSION["ErrorePassword"])) {
					echo $_SESSION["ErrorePassword"];
					?>
					<br>
					<br>
					<?php
					unset($_SESSION["ErrorePassword"]);
				}
				?>
			</div>
			<label>Immagine del profilo</label>
			<input type="file"  name="file"><br>
			<div id="ErroreLog">
				<!--errore upload file-->
				<?php
				if (isset($_SESSION["erroreUpload"])) {
					echo $_SESSION["erroreUpload"];
					?>
					<br>
					<br>
					<?php
					unset($_SESSION["erroreUpload"]);
				}
				?>
			</div>
			<!--pulsante per registrarsi-->
			<input type="submit" name="registrati" class="btn-login" id="registrati" value="registrati">
			<div id="VantaggiPremium">
				<h3>diventa premium perché....</h3>
				<ul>
					<li>crea infiniti blog</li>
					<li>personalizzazione dei blog</li>
					<li>segui utenti, temi e blog senza alcun limite</li>
				</ul>
				<h3>Solo 2,99€ al mese!</h3>
				<input type="button" name="premium"  class="btn-login" id="diventaPremiumRegistrazione" value="diventa Premium">
			</div>
			<!--errore dati carta-->
			<div id="ErroreLog">
				<?php
				if (isset($_SESSION["ErroreCarta"])) {
					echo $_SESSION["ErroreCarta"];
					?>
					<br>
					<br>
					<?php
					unset($_SESSION["ErroreCarta"]);
				} ?>
			</div>
			<div id="contenitorePremium" style="display: none;">
				<label for="carta">Numero carta:</label>
				<?php
				if (isset($_SESSION["carta"])) {
					$cartaS = $_SESSION["carta"]; ?>
					<input type="text" class="w3-input" name="carta" id="numeroCarta" value="<?php echo $cartaS ?>" maxlength="16"><br>
				<?php } else { ?>
					<input type="text" class="w3-input" name= "carta" id="numeroCarta" placeholder="XXXX-XXXX-XXXX-XXXX" maxlength="16"><br>
				<?php } ?>
				<label for="scadenza">Data scadenza:</label><br>
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
				<input type="text" class="w3-input" name="CVC" id="codiceSegreto" maxlength="3" placeholder="123"><br>
				<!--pulsante per registrarsi-->
				<input type="submit" name="registrati" class="btn-login" id="registratiPremium" value="registrati">
			</div>	
		</form>
	</div>
	<script type="text/javascript">
		$(document).ready(function(){
			$('#diventaPremiumRegistrazione').click(function(){
				$('#contenitorePremium').css({"display":"block"});
				$('#diventaPremiumRegistrazione').hide();
				$('#registrati').hide();
			});
			$('#diventaPremiumImpostazioni').click(function(){
				$('#premiumFormImpostazioni').css({"display":"block"});
				$('#diventaPremiumImpostazioni').hide();
			});	
			//chiamata ajax per controllare che il nickname sia valido
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
		});
	</script>
</body>
</html>