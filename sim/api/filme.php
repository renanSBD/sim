<?php require 'pages/header.php'; ?>

<?php
require'classes/filmes.class.php';
require'classes/usuarios.class.php';
$a = new Filmes();
$u = new Usuarios();

if(isset($_GET['id']) && !empty($_GET['id'])){
	$id = addslashes($_GET['id']);
}else{
	?>
	<script type="text/javascript">window.location.href="index.php";</script>
	<?php
	exit;
}
$info = $a->getFilme($id);
?>
	<div class="container-fluid">
		
		<div class="row">
		<div class="col-sm-5">
			
			<div class="carousel slide" data-ride="carousel" id="meuCarousel">
				<div class="carousel-inner" role="listbox">
					<?php foreach ($info['fotos'] as $chave => $foto): ?>
						<div class="item <?php echo($chave=='0')?'active':''; ?>">
							<img src="assets/img/anuncios/<?php echo $foto['url']; ?>"/>
						</div>
					<?php endforeach; ?>
				</div>
				<a class="left carousel-control" href="#meuCarousel" role="button" data-slide="prev"><span><</span></a>
				<a class="right carousel-control" href="#meuCarousel" role="button" data-slide="next"><span>></span></a>
			</div>

		</div>
		<div class="col-sm-7">
			<h1><?php echo $info['titulo']; ?></h1>
			<h4><?php echo utf8_encode($info['temas']); ?></h4>
			<h3>Nota: <?php echo number_format($info['nota'], 2); ?></h3>
			<h4><?php echo $info['telefone']; ?></h4>
		</div>
	</div>

<?php require 'pages/footer.php'; ?>