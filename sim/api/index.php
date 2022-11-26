<?php require 'pages/header.php'; ?>

<?php
require'classes/filmes.class.php';
require'classes/usuarios.class.php';
require'classes/temas.class.php';
$a = new Filmes();
$u = new Usuarios();
$c = new Temas();

$filtros = array(
	'temas'=>'',
	'valor'=>'',
);
if(isset($_GET['filtros'])){
	$filtros = $_GET['filtros'];
}

$total_filmes = $a->getTotalFilmes($filtros);
$total_usuarios = $u->getTotalUsuarios();

// criar paginação 
$p = 1;
if(isset($_GET['p']) && !empty($_GET['p'])){
	$p = addslashes($_GET['p']);
}
$por_pagina = 2;
$total_paginas = ceil($total_filmes / $por_pagina);// ceil arredonda para cima
//fim paginação


$filmes = $a->getUltimosFilmes($p, $por_pagina, $filtros);
$temas = $c->getLista();
?>
	<div class="container-fluid">
		<div class="jumbotron">
			<h2>Nós temos hoje <?php echo $total_filmes; ?> filmes.</h2>
			<p>E mais de <?php echo $total_usuarios; ?> usuários cadastrados.</p>
		</div>

		<div class="row">
		<div class="col-sm-3">
			<h4>Pesquisa Avançada</h4>
			<form method="GET">
				<div class="form-group">
					<label for="temas">Tema:</label>
					<select id="temas" name="filtros[temas]" class="form-control">
						<option></option>
						<?php foreach($temas as $tema):?>
							<option value="<?php echo $tema['id']; ?>" <?php echo ($tema['id'] == $filtros['temas'])?'selected="selected"':''; ?>><?php echo ($tema['nome']); ?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="form-group">
					<label for="nota">Notas:</label>
					<select id="nota" name="filtros[nota]" class="form-control">
						<option></option>
						<option value="0-2" <?php echo ($filtros['nota'] == '0-2')?'selected="selected"':''; ?>>0 - 2</option>
						<option value="3-5" <?php echo ($filtros['nota'] == '3-5')?'selected="selected"':''; ?>>3 - 5</option>
						<option value="6-7"<?php echo ($filtros['nota'] == '6-7')?'selected="selected"':''; ?>>6 - 7</option>
						<option value="8-10"<?php echo ($filtros['nota'] == '8-10')?'selected="selected"':''; ?>>8 - 10</option>
					</select>
				</div>
				<div class="form-group">
					<input type="submit" class="btn btn-info" value="Buscar"/>
				</div>
			</form>
		</div>
		<div class="col-sm-9">
			<h4>Últimos Filmes</h4>
			<table class="table table-striped">
				<tbody>
					<?php foreach($filmes as $filme): ?>
						<tr>
							<td>
								<?php if(!empty($anuncio['url'])): ?>
								<img src="assets/img/filmes/<?php echo $filme['url']; ?>" height="50px" border="0" />
								<?php else: ?>
								<img src="assets/img/default.jpg" height="50px" border="0" />
								<?php endif; ?> 
							</td>
							<td>
								<a href="filme.php?id=<?php echo $filme['id'];?>"><?php echo $filme['titulo']; ?></a><br/>
								<?php echo ($filme['temas']); ?>
							</td>
							<td>
								Nota: <?php echo number_format($filme['nota'], 2); ?>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>

			<ul class="pagination">
				<?php for($q=1;$q<=$total_paginas;$q++): ?>
					<li class="<?php echo($p == $q)?'active':''; ?>"><a href="index.php?<?php
					 $w = $_GET;
					 $w['p'] = $q;
					 echo http_build_query($w);
					?>"><?php echo $q; ?></a></li>
				<?php endfor; ?>
			</ul>
		</div>
	</div>
<?php require 'pages/footer.php'; ?>