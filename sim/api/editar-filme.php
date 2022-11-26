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
	if(isset($_FILES['fotos'])){
		$fotos = $_FILES['fotos'];
	}else{
		$fotos = array();
	}


	$a->editAnuncio($titulo, $temas, $nota, $fotos, $_GET['id']);
	?>
	<div class="alert alert-success">
		Filme editado com sucesso!
	</div>
	<?php
}
if(isset($_GET['id']) && !empty($_GET['id'])){
	$info = $a->getFilme($_GET['id']);
}else{
	?>
	<script type="text/javascript">window.location.href="meus-filmes.php";</script>
	<?php
	exit;
}

?>
<div class="container">
	<h1>Meus Filmes - Editar Filmes</h1>

	<form method="POST" enctype="multipart/form-data"><!--permite adicionar imagem no form-->

		<div class="form-group">
			<label for="temas">Temas:</label>
			<select name="temas" id="temas" class="form-control">
				<?php
					require 'classes/temas.class.php';
					$c = new Temas();
					$cats = $c->getLista();
					foreach ($cats as $cat):
				?>
				<option value="<?php echo $cat['id']; ?>"<?php echo($info['id_temas'] == $cat['id'])?'selected="selected"':''; ?>><?php echo utf8_encode($cat['nome']); ?>
				</option>
				<?php
					endforeach;
				?>
			</select>
		</div>
		<div class="form-group">
			<label for="titulo">Título:</label>
			<input type="text" name="titulo" id="titulo" class="form-control" value="<?php echo $info['titulo']; ?>" />
		</div>
		<div class="form-group">
			<label for="valor">Nota:</label>
			<input type="text" name="valor" id="valor" class="form-control" value="<?php echo $info['valor']; ?>" />
		</div>
		<div class="form-group">
			<label for="temas">Temas:</label>
			<textarea class="form-control" name="temas"><?php echo $info['temas']; ?></textarea>
		</div>
		<div class="form-group">
			<label for="add_fotos">Fotos do anúncio:</label>
			<input type="file" name="fotos[]" multiple /><br />

			<div class="panel panel-default">
				<div class="panel-heading">Fotos do Anúncio</div>
				<div class="panel-body">
				<?php foreach($info['fotos'] as $foto):?>
					<div class="foto_item">
						<img src="assets/img/anuncios/<?php echo $foto['url']; ?>" class="img-thumbnail" border="0" /><br/>
						<a href="excluir-foto.php?id=<?php echo $foto['id']; ?>" class="btn btn-default">Excluir Imagem</a>
					</div>
				<?php endforeach;?>
				</div>
			</div>
		</div>
		<input type="submit" value="Salvar" class="btn btn-default" />
	</form>

</div>
<?php require 'pages/footer.php'?>