<?php 
//mi connetto al database
include('connetti.php'); 
//se esiste la variabile nome e non è vuota preparo la query
if(isset($_POST['nome']) && $_POST["nome"] != "" && (preg_match('/^([A-Za-z0-9\'àèìòù:!?,]+\s*)+$/i', $_POST["nome"]))){
	$nome = $_POST['nome'];
	//cerco all'interno del nome utente
	$sql = $mysqli -> prepare("SELECT nickname, idutente FROM utente WHERE nickname LIKE CONCAT('%',?,'%')");
	$sql -> bind_param('s', $nome);
	$sql -> execute(); 
	$result = $sql -> get_result();
	//cerco all'interno del titolo del blog
	$sqlBlog = $mysqli -> prepare("SELECT idblog, titolo FROM blog WHERE titolo LIKE CONCAT('%',?,'%')");
	$sqlBlog -> bind_param('s', $nome);
	$sqlBlog -> execute(); 
	$resultBlog = $sqlBlog -> get_result();
	//cerco all'interno del nome del tema
	$sqlTema = $mysqli -> prepare("SELECT idtema, nometema FROM tema WHERE nometema LIKE CONCAT('%',?,'%')");
	$sqlTema -> bind_param('s', $nome);
	$sqlTema -> execute(); 
	$resultTema = $sqlTema -> get_result();
	//mostro i risultati
	if($result -> num_rows > 0){
		while ($row = $result -> fetch_assoc()) { ?>
			<a href="profilo.php?id=<?php echo $row["idutente"]?>"><?php echo $row["nickname"]?></a><br>
	<?php }
	} else {
		echo "Non sono stati trovati utenti per '".$nome."'" ?> <br> <?php ;
	}
	if($resultBlog -> num_rows > 0){
		while($rowBlog = $resultBlog -> fetch_assoc()) { ?>
			<a href="blog.php?id=<?php echo $rowBlog["idblog"]?>"><?php echo $rowBlog["titolo"]?></a><br>
	<?php }
	} else {
		echo "Non sono stati trovati blog per '".$nome."'" ?> <br> <?php ;
	}
	if($resultTema -> num_rows > 0){
		while($rowTema = $resultTema -> fetch_assoc()) { ?>
			<a href="tema.php?id=<?php echo $rowTema["idtema"]?>"><?php echo $rowTema["nometema"]?></a><br>
	<?php }
	} else {
		echo "Non sono stati trovati temi per '".$nome."'";
	}
} else {
	echo "Non sono stati trovati risultati";
}
?>