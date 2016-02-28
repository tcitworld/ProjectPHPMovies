<?php

use Csanquer\Silex\PdoServiceProvider\Provider\PDOServiceProvider;
use Symfony\Component\HttpFoundation\Request;
use Silex\Application;

require_once __DIR__.'/../vendor/autoload.php'; 

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
            'host'     => 'localhost',
            'dbname'   => 'Moviez',
            'port'     => 3306,
            'user'     => 'root',
            'password' => 'Lory1992/',
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

// get PDO connection
$pdo = $app['pdo'];

$app->get('/view/{id}', function($id) use($app) {
	$query = $app['pdo']->prepare('SELECT * FROM films WHERE code_film=?');
	$query->execute(array($id));
	$res = $query->fetch();
    return $app['twig']->render('view.twig',array('film' => $res)); 
}); 

$app->get('/{page}', function($page) use($app) {
	$query = $app['pdo']->prepare('SELECT * FROM films');
	$query->execute();
	$res = $query->fetchAll();
    $nbRows = $query->rowCount();
	return $app['twig']->render('index.twig',array('films' => $res));
})->value('page',1);

$app->post('/create', function(Request $request) use($app) {
	$titrefr = $request->get('titrefr');
	$titrevo = $request->get('titrevo');
	$couleur = $request->get('couleur');
	$pays = $request->get('pays');
    $date = $request->get('date');
    $duree = $request->get('duree');

	$query = $app['pdo']->prepare('INSERT INTO films VALUES (0,?,?,?,?,?,?,NULL,NULL)');
	$query->execute(array($titrevo,$titrefr,$pays,$date,$duree,$couleur));

});

$app->get('/createForm', function(Request $request) use($app) {
	return $app['twig']->render('newMembre.twig');
});

$app->get('/delete/{id}', function($id) use($app) {
	$query = $app['pdo']->prepare('DELETE FROM films WHERE code_film=?');
	$query->execute(array($id));
	return 'Le membre a bien été supprimé<br><a href="../">Retour à l\'accueil</a>';
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
    return json_encode(array('ok'));
});

$app->run(); 