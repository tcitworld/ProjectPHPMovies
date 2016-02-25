<?php

use Csanquer\Silex\PdoServiceProvider\Provider\PDOServiceProvider;
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
            'host'     => 'mysql',
            'dbname'   => 'servinfo-db',
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

// get PDO connection
$pdo = $app['pdo'];

$app->get('/hello/{name}', function($name) use($app) {
	$query = $pdo->prepare('SELECT * FROM CARNET WHERE NOM=?');
	$query->execute(array(upper($name)));
    return 'Hello '.$app->escape($query->fetchAll()); 
}); 

$app->run(); 