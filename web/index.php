<?php

use Csanquer\Silex\PdoServiceProvider\Provider\PDOServiceProvider;
use Symfony\Component\HttpFoundation\Request;
use Silex\Application;

require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/../Model/Films.class.php';

$app = new Application(); 
$app['debug'] = true;

ini_set('display_errors', 1);
error_reporting(-1);
error_reporting(E_ALL ^ E_STRICT);

$app->register(
    // you can customize services and options prefix with the provider first argument (default = 'pdo')
    new PDOServiceProvider('pdo'),
    array(
        'pdo.server'   => array(
            // PDO driver to use among : mysql, pgsql , oracle, mssql, sqlite, dblib
            'driver'   => 'mysql',
            'host'     => 'servinfo-db',
            'dbname'   => 'dbcitharel',
            'port'     => 3306,
            'user'     => 'citharel',
            'password' => 'mdpmysql',
        ),
        // optional PDO attributes used in PDO constructor 4th argument driver_options
        // some PDO attributes can be used only as PDO driver_options
        // see http://www.php.net/manual/fr/pdo.construct.php
        'pdo.options' => array(
            \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'"
        ),
        // optional PDO attributes set with PDO::setAttribute
        // see http://www.php.net/manual/fr/pdo.setattribute.php
        'pdo.attributes' => array(
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
        ),
    )
);

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/../views',
));


$app->get('/', function() use($app) {
    $films = new Films($app['pdo']);
	return $app['twig']->render('index.twig',array('films' => $films->getFilms()));
});

$app->post('/create', function(Request $request) use($app) {
	$titrefr = $request->get('titrefr');
	$titrevo = $request->get('titrevo');
	$couleur = $request->get('couleur');
	$pays = $request->get('pays');
    $date = $request->get('date');
    $duree = $request->get('duree');

    $films = new Films($app['pdo']);
	$films->newFilm(array($titrevo,$titrefr,$pays,$date,$duree,$couleur));
    return $app->json(array('ok'));

});

$app->get('/details/{id}', function($id) use($app) {
    $films = new Films($app['pdo']);
    return $app->json(array("genres" => $films->getGenres($id), "acteurs" => $films->getActeurs($id)));
});

$app->get('/delete/{id}', function($id) use($app) {
	$query = $app['pdo']->prepare('DELETE FROM films WHERE code_film=?');
	$query->execute(array($id));
	return $app->json(array('ok'));
});

$app->post('/edit/{id}', function(Request $request, $id) use($app) {
	$query = $app['pdo']->prepare('UPDATE films SET titre_original=:titrevo, titre_francais=:titrefr, pays=:pays, date=:date, duree=:duree, couleur=:couleur WHERE code_film=:codefilm');
    $query->bindParam(':titrevo', $request->get('titrevo'));
    $query->bindParam(':titrefr', $request->get('titrefr'));
    $query->bindParam(':couleur', $request->get('couleur'));
    $query->bindParam(':pays', $request->get('pays'));
    $query->bindParam(':date', $request->get('date'));
    $query->bindParam(':duree', $request->get('duree'));
    $query->bindParam(':codefilm', $id);
	$query->execute();
    return $app->json(array('ok'));
});

$app->run(); 
