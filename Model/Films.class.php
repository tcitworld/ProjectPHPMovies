<?php

/**
 * Class Films
 */
class Films
{
	private $pdo;

	function __construct($pdo)
	{
		$this->pdo = $pdo;
	}

	public function getFilms() {
		$query = $this->pdo->prepare('SELECT DISTINCT * from films INNER JOIN individus ON films.realisateur = individus.code_indiv');
		$query->execute();
		return $query->fetchAll();
	}

	public function getFilm($filmId) {
		$query = $this->pdo->prepare('SELECT DISTINCT * from films INNER JOIN individus ON films.realisateur = individus.code_indiv WHERE code_film=?');
		$query->execute(array($filmId));
		return $query->fetch();
	}

	public function getActeurs($filmId) {
		$query = $this->pdo->prepare('SELECT DISTINCT nom,prenom,nationalite,date_naiss,date_mort FROM acteurs LEFT JOIN individus ON acteurs.ref_code_acteur=individus.code_indiv WHERE ref_code_film=?');
		$query->execute(array($filmId));
		return $query->fetchAll();
	}

	public function getReals() {
		$query = $this->pdo->prepare('SELECT DISTINCT code_indiv, nom, prenom from individus RIGHT JOIN films ON individus.code_indiv = films.realisateur ORDER BY nom');
		$query->execute();
		return $query->fetchAll();
	}

	public function getGenres($filmId) {
		$query = $this->pdo->prepare('SELECT DISTINCT nom_genre FROM classification LEFT JOIN genres ON classification.ref_code_genre=genres.code_genre WHERE ref_code_film=?');
		$query->execute(array($filmId));
		return $query->fetchAll();
	}

	public function newFilm($fields) {
		$query = $this->pdo->prepare('INSERT INTO films VALUES (0,?,?,?,?,?,?,?,?)');
		$query->execute($fields);
	}

	public function editFilm($titrevo, $titrefr, $couleur, $pays, $date, $duree, $real, $id) {
		$query = $this->pdo->prepare('UPDATE films SET titre_original=:titrevo, titre_francais=:titrefr, pays=:pays, date=:date, duree=:duree, couleur=:couleur, realisateur=:real WHERE code_film=:codefilm');
		$query->bindParam(':titrevo', $titrevo);
	    $query->bindParam(':titrefr', $titrefr);
	    $query->bindParam(':couleur', $couleur);
	    $query->bindParam(':pays', $pays);
	    $query->bindParam(':date', $date);
	    $query->bindParam(':duree', $duree);
	    $query->bindParam(':real', $real);
	    $query->bindParam(':codefilm', $id);
	    $query->execute();
	}

	public function delete($filmId) {
		$query = $this->pdo->prepare('DELETE FROM films WHERE code_film=?');
		$query->execute(array($filmId));
	}

}