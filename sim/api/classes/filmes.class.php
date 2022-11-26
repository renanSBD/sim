 <?php
class Filmes{
	// retorna a quantidade de filmes cadastrados no bd
	public function getTotalFilmes($filtros){
		global $pdo;

		$filtrostring = array('1=1');
		if(!empty($filtros['temas'])){
			$filtrostring[] = 'filmes.id_temas = :id_temas';
		}
		if(!empty($filtros['nota'])){
			$filtrostring[] = 'filmes.nota BETWEEN :nota1 AND :nota2';
		}

		$sql = $pdo->prepare("SELECT COUNT(*) as c FROM filmes WHERE ".implode(' AND ', $filtrostring));
		if(!empty($filtros['temas'])){
			$sql->bindValue(':id_temas', $filtros['temas']);
		}
		if(!empty($filtros['nota'])){
			$nota = explode('-', $filtros['nota']);
			$sql->bindValue(':nota1', $nota['0']);
			$sql->bindValue(':nota2', $nota['1']);
		}

		$sql->execute();
		$row = $sql->fetch();
		return $row['c'];
	}

	public function getUltimosFilmes($page, $perPage, $filtros){
		global $pdo;

		$offset = ($page - 1) * $perPage;

		$array = array();
		//buscar utilizando os filtros predefinidos
		$filtrostring = array('1=1');
		if(!empty($filtros['temas'])){
			$filtrostring[] = 'filmes.id_temas = :id_temas';
		}
		if(!empty($filtros['nota'])){
			$filtrostring[] = 'filmes.nota BETWEEN :nota1 AND :nota2';
		}

		$sql = $pdo->prepare("SELECT
		 *,
		  (select filmes_imagens.url_imagem from filmes_imagens where filmes_imagens.id_filmes = filmes.id limit 1) as url, 
		  (select temas.nome from temas where temas.id = filmes.id_temas) as temas
		  FROM filmes WHERE ".implode(' AND ', $filtrostring)." ORDER BY id DESC LIMIT $offset, $perPage");//ordena decrescente

		if(!empty($filtros['temas'])){
			$sql->bindValue(':id_temas', $filtros['temas']);
		}
		if(!empty($filtros['nota'])){
			$nota = explode('-', $filtros['nota']);
			$sql->bindValue(':nota1', $nota['0']);
			$sql->bindValue(':nota2', $nota['1']);
		}

		$sql->execute();

		if($sql->rowCount() > 0){
			$array = $sql->fetchAll();
		}
		return $array;

	}

	public function getMeusFilmes(){
		global $pdo;

		$array = array();
		$sql = $pdo->prepare("SELECT
		 *,
		  (select filmes_imagens.url from filmes_imagens where filmes_imagens.id_filmes = filmes.id limit 1) as url 
		  FROM filmes 
		  WHERE id_usuario = :id_usuario");
		$sql->bindValue(":id_usuario", $_SESSION['cLogin']);
		$sql->execute();

		if($sql->rowCount() > 0){
			$array = $sql->fetchAll();
		}
		return $array;
	} 
	public function getFilme($id){
		$array = array();
		global $pdo;

		$sql = $pdo->prepare("SELECT 
			*,
			(select temas.nome from temas where temas.id = filmes.id_temas) as temas, 
			(select usuarios.telefone from usuarios where usuarios.id = filmes.id_usuario) as telefone
			FROM filmes WHERE id = :id");
		$sql->bindValue(":id", $id);
		$sql->execute();

		if($sql->rowCount() > 0){
			$array = $sql->fetch();
			//mostrar todas as imagens cadastradas
			$array['fotos'] = array();
			$sql =$pdo->prepare("SELECT id, url FROM filmes_imagens WHERE id_filmes = :id_filmes");
			$sql->bindValue(":id_filmes", $id);
			$sql->execute();

			if($sql->rowCount() > 0){
				$array['fotos'] = $sql->fetchAll();
			}
		}
		return $array;

	}
	public function addFilme($titulo, $temas, $nota){
		global $pdo;

		$sql = $pdo->prepare("INSERT INTO filmes SET titulo = :titulo, temas = :temas, nota = :nota");
		$sql->bindValue(":titulo", $titulo);
		$sql->bindValue(":temas", $temas);
		$sql->bindValue(":nota", $nota);
		$sql->execute();
	}
	public function editFilme($titulo, $temas, $nota, $fotos, $id){
		global $pdo;

		$sql = $pdo->prepare("UPDATE filmes SET titulo = :titulo, temas = :temas, usuario = :usuario, nota = :nota WHERE id = :id");
		$sql->bindValue(":titulo", $titulo);
		$sql->bindValue(":temas", $temas);
		$sql->bindValue(":nota", $nota);
		$sql->bindValue(":id", $id);
		$sql->execute();
		//inserir a imagem 
		if(count($fotos) > 0){ 
			for($q=0;$q<count($fotos['tmp_name']);$q++){
				$tipo = $fotos['type'][$q];
				if(in_array($tipo, array('image/jpeg', 'image/png'))){
					$tmpname = md5(time().rand(0,9999)).'.jpg';
					move_uploaded_file($fotos['tmp_name'][$q], 'assets/img/filmes/'.$tmpname);

					list($width_orig, $height_orig) = getimagesize('assets/img/filmes/'.$tmpname);
					$ratio = $width_orig/$height_orig;

					$width = 500;
					$height = 500;

					if($width/$height > $ratio){
						$width = $height*$ratio;
					}else{
						$height = $width/$ratio;
					}	

					$img = imagecreatetruecolor($width, $height);
					if($tipo == 'image/jpeg'){
						$origi = imagecreatefromjpeg('assets/img/filmes/'.$tmpname);
					}elseif ($tipo == 'image/png') {
						$origi = imagecreatefrompng('assets/img/filmes/'.$tmpname);
					}

					imagecopyresampled($img, $origi, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);
					//imagem salva no servidor
					imagejpeg($img, 'assets/img/filmes/'.$tmpname, 80);
					//salvar no banco de dados a url
					$sql = $pdo->prepare("INSERT INTO filmes_imagens SET id_filmes = :id_filmes, url = :url");
					$sql->bindValue(":id_filmes", $id);
					$sql->bindValue(":url", $tmpname);
					$sql->execute();
				}
			}
			//print_r($fotos);
			//exit;
		}
	}



	public function excluirFilme($id){
		global $pdo;
		$sql = $pdo->prepare("DELETE FROM filmes_imagens WHERE id_filmes = :id_filmes");
		$sql->bindValue(":id_filmes", $id);
		$sql->execute();

		$sql = $pdo->prepare("DELETE FROM filmes WHERE id = :id");
		$sql->bindValue(":id", $id);
		$sql->execute();

	}

	public function excluirFoto($id){
		global $pdo;

		$id_filmes = 0;

		$sql = $pdo->prepare("SELECT id_filmes FROM filmes_imagens WHERE id = :id");
		$sql->bindValue(":id", $id);
		$sql->execute();

		if($sql->rowCount() > 0){
			$row = $sql->fetch();
			$id_filmes = $row['id_filmes'];
		}

		$sql = $pdo->prepare("DELETE FROM filmes_imagens WHERE id = :id");
		$sql->bindValue(":id", $id);
		$sql->execute();

		return $id_filmes;
	}
}

?>