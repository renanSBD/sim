<?php require 'pages/header.php'; ?>
<?php
if(empty($_SESSION['cLogin'])){
	?>
	<script type="text/javascript">window.location.href="login.php";</script>
	<?php
	exit;
}

require 'classes/filmes.class.php';
$a = new Filmes();
if(isset($_POST['titulo']) && !empty($_POST['titulo'])){
	$titulo = addslashes($_POST['titulo']);
	$temas = addslashes($_POST['temas']);
	$nota = addslashes($_POST['nota']);

	$a->addAnuncio($titulo, $tema, $nota);
	?>
	<div class="alert alert-success">
		Filme adicionado com sucesso!
	</div>
	<?php
}


?>
<div class="container">
	<h1>Meus Filmes - Adicionar Filmes</h1>

	<form method="POST" enctype="multipart/form-data"><!--permite adicionar imagem no form-->

		<div class="form-group">
			<label for="temas">temas:</label>
			<select name="temas" id="temas" class="form-control">
				<?php
					require 'classes/temas.class.php';
					$c = new temas();
					$cats = $c->getLista();
					foreach ($cats as $cat):
				?>
				<option value="<?php echo $cat['id']; ?>"><?php echo utf8_encode($cat['nome']); ?>
				</option>
				<?php
					endforeach;
				?>
			</select>
		</div>
		<div class="form-group">
			<label for="titulo">Título:</label>
			<input type="text" name="titulo" id="titulo" class="form-control" />
		</div>
		<div class="form-group">
			<label for="nota">Nota:</label>
			<input type="text" name="nota" id="nota" class="form-control" />
		</div>
		<div class="form-group">
			<label for="temas">Temas:</label>
			<textarea class="form-control" name="temas"></textarea>
		</div>
		<input type="submit" value="Adicionar" class="btn btn-default" />
	</form>

</div>
<?php require 'pages/footer.php'?>