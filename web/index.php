<?php

use Csanquer\Silex\PdoServiceProvider\Provider\PDOServiceProvider;
use Symfony\Component\HttpFoundation\Request;
use Silex\Application;

require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/../Model/Films.class.php';

$app = new Application(); 

$app->register(
    new PDOServiceProvider('pdo'),
    array(
        'pdo.server'   => array(
            /**
             *
             * Paramétrages base de données
             *
             * - host : localhost/servinfo-db
             * - dbname : nom de la base de données
             * - user : votre user
             * - password : le mot de passe associé à cet user
             * 
             * Les autres paramètres sont à laisser par défaut.
             *
             */
            'driver'   => 'mysql',
            'host'     => 'localhost',
            'dbname'   => '',
            'port'     => 3306,
            'user'     => '',
            'password' => '',
        ),
        'pdo.options' => array(
            \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'"
        ),
        'pdo.attributes' => array(
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
        ),
    )
);

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/../views',
));

/**
 *
 * Liste les films
 * 
 * @route /
 *
 * @return html
 *
 */
$app->get('/', function() use($app) {
    $films = new Films($app['pdo']);
	return $app['twig']->render('index.twig',array('films' => $films->getFilms(), 'reals' => $films->getReals()));
});

/**
 *
 * Crée un film
 *
 * @method POST
 * @route /create
 * @param str titrefr
 * @param str titrevo
 * @param str couleur
 * @param str pays
 * @param str date
 * @param int duree
 * @param int real
 * 
 * @return json
 *
 */
$app->post('/create', function(Request $request) use($app) {
	$titrefr = $request->get('titrefr');
	$titrevo = $request->get('titrevo');
	$couleur = $request->get('couleur');
	$pays = $request->get('pays');
    $date = $request->get('date');
    $duree = $request->get('duree');
    $real = $request->get('real');

    $films = new Films($app['pdo']);
	$films->newFilm(array($titrevo,$titrefr,$pays,$date,$duree,$couleur,$real,''));
    return $app->json(array('ok'));

});

/**
 *
 * Donne des détails pour un film
 *
 * @method GET
 * @route /details/{id}
 * @param int $id
 *
 * @return json
 *
 */
$app->get('/details/{id}', function($id) use($app) {
    $films = new Films($app['pdo']);
    return $app->json(array("genres" => $films->getGenres($id), "acteurs" => $films->getActeurs($id), "film" => $films->getFilm($id)));
});

/**
 *
 * Supprime un film
 *
 * @method GET
 * @route /delete/{id}
 * @param int $id
 *
 * @return json
 *
 */
$app->get('/delete/{id}', function($id) use($app) {
	$films = new Films($app['pdo']);
    $films->delete($id);
	return $app->json(array('ok'));
});

/**
 *
 * Edite un film
 *
 * @method POST
 * @route /edit/{id}
 * @param int $id
 *
 * @return json
 *
 */
$app->post('/edit/{id}', function(Request $request, $id) use($app) {
    $films = new Films($app['pdo']);
    $films->editFilm($request->get('titrevo'), $request->get('titrefr'), $request->get('couleur'), $request->get('pays'), $request->get('date'), $request->get('duree'), $request->get('real'), $id);
    return $app->json(array('ok'));
});

$app->run(); 
