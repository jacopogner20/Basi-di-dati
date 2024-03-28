<?php
//mi connetto al database
session_start();
//mi connetto ai database
include('connetti.php');
//estraggo i dati dal POST
extract($_POST);
//utente segue un blog
if(isset($_POST["idblog"], $_POST["idutente"])){
	$idBlog = $_POST["idblog"];
	$idUtente = $_POST["idutente"];
	$queryInsLike = "INSERT INTO segueblog(idblog, idutente) VALUES('$idBlog', '$idUtente')";
	$resInsLike = $mysqli -> query($queryInsLike);
}
//utente segue un altro utente
if(isset($_POST["idfollower"], $_POST["idseguito"])){
	$idFollower = $_POST["idfollower"];
	$idSeguito = $_POST["idseguito"];
	$queryFollowa = "INSERT INTO segui(idfollower, idseguito) VALUES('$idFollower','$idSeguito')";
	$resFollowa = $mysqli -> query($queryFollowa);
}
//utente segue un tema
if(isset($_POST["idFollowerTema"], $_POST["idtema"])){
	$idFolloWTh = $_POST["idFollowerTema"];
	$idTema = $_POST["idtema"];
	$queryFollowaTema = "INSERT INTO seguetema(idutente, idtema) VALUES('$idFolloWTh','$idTema')";
	$resFollowaTema = $mysqli -> query($queryFollowaTema);
}
//utente non segue più un utente
if(isset($_POST["idfollowerNON"], $_POST["idseguitoNON"])){
	$idFollowerNON = $_POST["idfollowerNON"];
	$idSeguitoNON = $_POST["idseguitoNON"];
	$querySmettiSeguireUt = "DELETE FROM segui WHERE idfollower = '$idFollowerNON' AND idseguito = '$idSeguitoNON'";
	$resSmettiSeguireUt = $mysqli -> query($querySmettiSeguireUt);
} 
//utente non segue più il blog
if(isset($_POST["idfollowerNONBlog"], $_POST["idseguitoNONBlog"])){
	$idFollowerNONBlog = $_POST["idfollowerNONBlog"];
	$idSeguitoNONBlog = $_POST["idseguitoNONBlog"];
	$querySmettiSeguireBlog = "DELETE FROM segueblog WHERE idblog = '$idSeguitoNONBlog' AND idutente = '$idFollowerNONBlog'";
	$resSmettiSeguireBlog = $mysqli -> query($querySmettiSeguireBlog);
} 
//utente non segue più il tema
if(isset($_POST["idfollowerNONTema"], $_POST["idseguitoNONTema"])){
	$idFollowerNONTema = $_POST["idfollowerNONTema"];
	$idSeguitoNONTema = $_POST["idseguitoNONTema"];
	$querySmettiSeguireTema = "DELETE FROM seguetema WHERE idtema = '$idSeguitoNONTema' AND idutente = '$idFollowerNONTema'";
	$resSmettiSeguireTema = $mysqli -> query($querySmettiSeguireTema);
} 
//utente mette like a un post
if (isset($_POST["idutenteLike"], $_POST["idBtnlike"])) {
	$idLiker = $_POST["idutenteLike"];
	$idPostLiked = $_POST["idBtnlike"];
	$queryLikePost = "INSERT INTO piaciuti(idutente, idpost) VALUES('$idLiker','$idPostLiked')";
	$resLikePost = $mysqli -> query($queryLikePost);
}
//utente toglie il like a un post
if (isset($_POST["idutenteLikeNON"], $_POST["idBtnlikeNON"])) {
	$idLikerNON = $_POST["idutenteLikeNON"];
	$idPostLikedNON = $_POST["idBtnlikeNON"];
	$queryLikePostNON = "DELETE FROM piaciuti WHERE idpost = '$idPostLikedNON' AND idutente = '$idLikerNON'";
	$resLikePostNON = $mysqli -> query($queryLikePostNON);
}
//utente mette like a un post dalla home
if (isset($_POST["idPostHome"], $_POST["idutenteHome"])) {
	$UtenteHome = $_POST["idutenteHome"];
	$PostHome = $_POST["idPostHome"];
	$queryInsLikeHome = "INSERT INTO piaciuti(idutente, idpost) VALUES('$UtenteHome', '$PostHome')";
	$resInsLikeHome = $mysqli -> query($queryInsLikeHome);
}
//utente toglie il like a un post dalla home
if(isset($_POST["idfollowerNONPostHome"], $_POST["idseguitoNONPostHome"])){
	$idFollowerNONPostHome = $_POST["idfollowerNONPostHome"];
	$idSeguitoNONPostHome = $_POST["idseguitoNONPostHome"];
	$querySmettiSeguirePostHome = "DELETE FROM piaciuti WHERE idpost='$idSeguitoNONPostHome' AND idutente = '$idFollowerNONPostHome'";
	$resSmettiSeguirePostHome = $mysqli -> query($querySmettiSeguirePostHome);
}
?>