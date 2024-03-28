<?php
session_start();
//mi connetto al database
include('connetti.php');
//estraggo i dati dal POST
extract($_POST);
$AutoC = $_POST["keyword"];
//se i caratteri del nome del tema sono corretti controllo se esistono temi con all'interno quei caratteri
if(isset($_POST["keyword"]) && $_POST["keyword"] != "" && (preg_match('/^[A-Za-z\'-]+$/i', $AutoC))) {
	$queryAutocompletamento = $mysqli -> prepare("SELECT * FROM tema WHERE nometema LIKE CONCAT('%',?,'%') ORDER BY nometema LIMIT 0,6");
	$queryAutocompletamento -> bind_param('s', $AutoC);
	$queryAutocompletamento -> execute();
	$resultAutocompletamento = $queryAutocompletamento -> get_result();
	if(isset($resultAutocompletamento)) { ?>
		<h3>Temi con '<?php echo $AutoC ?>'</h3>
		<?php
		//mostro i temi con quei caratteri nel nome
		while($rowAutocompletamento = $resultAutocompletamento -> fetch_assoc()) { 	?>				
			<p id="Cliccami"><?php echo $rowAutocompletamento["nometema"];?></p>
		<?php } 
	}
} 
?>