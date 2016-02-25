<?php

use Csanquer\Silex\PdoServiceProvider\Provider\PDOServiceProvider;
use Symfony\Component\HttpFoundation\Request;
use Silex\Application;

require_once __DIR__.'/../vendor/autoload.php'; 

$app = new Application(); 
$app['debug'] = true;

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

// get PDO connection
$pdo = $app['pdo'];

$app->get('/view/{id}', function($id) use($app) {
	$query = $app['pdo']->prepare('SELECT * FROM films WHERE code_film=?');
	$query->execute(array($id));
	$res = $query->fetch();
    return $app['twig']->render('view.twig',array('film' => $res)); 
}); 

$app->get('/', function() use($app) {
	$query = $app['pdo']->prepare('SELECT * FROM films LIMIT 0,10');
	$query->execute();
	$res = $query->fetchAll();
	return $app['twig']->render('index.twig',array('films' => $res));
});

$app->post('/create', function(Request $request) use($app) {
	$nomMembre = $request->get('nom');
	$prenomMembre = $request->get('prenom');
	$naissanceMembre = date('Y-m-d',strtotime($request->get('naissance')));
	$villeMembre = $request->get('ville');

	$query = $app['pdo']->prepare('INSERT INTO CARNET VALUES (0,?,?,?,?)');
	$query->execute(array($nomMembre,$prenomMembre,$naissanceMembre,$villeMembre));

	return 'Le membre a bien été inséré !<br><a href="../">Retour à l\'accueil</a>';
});

$app->get('/createForm', function(Request $request) use($app) {
	return $app['twig']->render('newMembre.twig');
});

$app->get('/delete/{id}', function($id) use($app) {
	$query = $app['pdo']->prepare('DELETE FROM CARNET WHERE ID=?');
	$query->execute(array($id));
	return 'Le membre a bien été supprimé<br><a href="../">Retour à l\'accueil</a>';
});

$app->post('/update/{id}', function($id) use($app) {
	$query = $app['pdo']->prepare('UPDATE CARNET WHERE ID=?');
	$query->execute(array($id));
});

$app->get('/updateForm/{id}', function($id) use($app) {
	$query = $app['pdo']->prepare('SELECT * FROM CARNET WHERE ID=?');
	$query->execute(array($id));
	$res = $query->fetch();
    return $app['twig']->render('edit.twig',array('membre' => $res));
});

$app->run(); 