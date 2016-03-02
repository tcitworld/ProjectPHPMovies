<?php

/**
 * Class Films
 */
class Films
{
	private $pdo;
	private $acteurs;
	private $genres;

	function __construct($pdo)
	{
		$this->pdo = $pdo;
		$this->acteurs = array();
		$this->genres = array();
	}

	public function getFilms() {
		$query = $this->pdo->prepare('SELECT DISTINCT * from films');
		$query->execute();
		return $query->fetchAll();
	}

	public function getActeurs($filmId) {
		$query = $this->pdo->prepare('SELECT DISTINCT nom,prenom,nationalite,date_naiss,date_mort FROM acteurs LEFT JOIN individus ON acteurs.ref_code_acteur=individus.code_indiv WHERE ref_code_film=?');
		$query->execute(array($filmId));
		return $query->fetchAll();
	}

	public function getGenres($filmId) {
		$query = $this->pdo->prepare('SELECT DISTINCT nom_genre FROM classification LEFT JOIN genres ON classification.ref_code_genre=genres.code_genre WHERE ref_code_film=?');
		$query->execute(array($filmId));
		return $query->fetchAll();
	}

	public function newFilm($fields) {
		$query = $app['pdo']->prepare('INSERT INTO films VALUES (0,?,?,?,?,?,?,NULL,NULL)');
		$query->execute($fields);
	}

}