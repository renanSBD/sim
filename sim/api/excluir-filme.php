<?php
require 'config.php';
if(empty($_SESSION['cLogin'])){
	header("Location: login.php");
	exit;
}

require 'classes/filmes.class.php';
$a = new Filmes();

if(isset($_GET['id']) && !empty($_GET['id'])){
	$a->excluirFilme($_GET['id']);
}
header("Location: meus-filmes.php");
?>