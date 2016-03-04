<?php

/**
 *
 * Class Films
 *
 * @author Thomas Citharel
 * @author Lory Amaïzo
 *
 */
class Films
{
	private $pdo;

	function __construct($pdo)
	{
		$this->pdo = $pdo;
	}

	/**
	 *
	 * function getFilms
	 * 		Retourne tous les films
	 *
	 * @return Array
	 *
	 */
	public function getFilms() {
		$query = $this->pdo->prepare('SELECT DISTINCT * from films INNER JOIN individus ON films.realisateur = individus.code_indiv');
		$query->execute();
		return $query->fetchAll();
	}

	/**
	 *
	 * function getFilm
	 *		Retourne tous les détails d'un film
	 *
	 * @param int $filmId
	 *
	 * @return Array
	 *
	 */
	public function getFilm($filmId) {
		$query = $this->pdo->prepare('SELECT DISTINCT * from films INNER JOIN individus ON films.realisateur = individus.code_indiv WHERE code_film=?');
		$query->execute(array($filmId));
		return $query->fetch();
	}

	/**
	 *
	 * function getActeurs
	 * 		Retourne la liste des acteurs
	 *
	 * @param int $filmId
	 *
	 * @return Array
	 *
	 */
	public function getActeurs($filmId) {
		$query = $this->pdo->prepare('SELECT DISTINCT nom,prenom,nationalite,date_naiss,date_mort FROM acteurs LEFT JOIN individus ON acteurs.ref_code_acteur=individus.code_indiv WHERE ref_code_film=?');
		$query->execute(array($filmId));
		return $query->fetchAll();
	}

	/**
	 *
	 * function getReals
	 *		Retourne la liste de tous les réalisateurs (c'est à dire tous les individus présents dans la table films)
	 *
	 * @return Array
	 *
	 */
	public function getReals() {
		$query = $this->pdo->prepare('SELECT DISTINCT code_indiv, nom, prenom from individus RIGHT JOIN films ON individus.code_indiv = films.realisateur ORDER BY nom');
		$query->execute();
		return $query->fetchAll();
	}

	
	/**
	 * 
	 * function getGenres
	 *		Retourne les genres pour un film
	 *
	 * @param int $filmId
	 *
	 * @return Array
	 *
	 */
	public function getGenres($filmId) {
		$query = $this->pdo->prepare('SELECT DISTINCT nom_genre FROM classification LEFT JOIN genres ON classification.ref_code_genre=genres.code_genre WHERE ref_code_film=?');
		$query->execute(array($filmId));
		return $query->fetchAll();
	}

	/**
	 *
	 * function newFilm
	 *		Ajoute un film
	 *
	 * @param <Mixed>Array $fields
	 *
	 */
	public function newFilm($fields) {
		$query = $this->pdo->prepare('INSERT INTO films VALUES (0,?,?,?,?,?,?,?,?)');
		$query->execute($fields);
	}

	/**
	 *
	 * function editFilm
	 * 		Edite un film
	 *
	 * @param str $titrevo
	 * @param str $titrefr
	 * @param str $couleur
	 * @param str $pays
	 * @param int $date
	 * @param int $duree
	 * @param int $real
	 * @param int id
	 *
	 */
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

	/**
	 *
	 * function delete
	 * 		supprime un film
	 *
	 * @param int $filmID
	 * 
	 */
	public function delete($filmId) {
		$query = $this->pdo->prepare('DELETE FROM films WHERE code_film=?');
		$query->execute(array($filmId));
	}

}