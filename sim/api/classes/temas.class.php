<?php
class Temas{
	public function getLista(){
		$array = array();
		global $pdo;

		$sql = $pdo->query("SELECT * FROM temas");
		if($sql->rowCount() > 0){
			$array = $sql->fetchAll();
		}
		return $array;
	}
}

?>