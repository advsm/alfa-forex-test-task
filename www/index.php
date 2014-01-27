<?php
/**
 * Silex index file.
 *
 * @link http://silex.sensiolabs.org/
 */

use Symfony\Component\HttpFoundation\Request;

define('BASE_PATH', realpath('../'));
require_once BASE_PATH . '/vendor/autoload.php';
require_once BASE_PATH . '/classes/ProductProperty.php';

$app = new Silex\Application();
$app['debug'] = true;

/**
 * Register Template Engine. Set views path.
 */
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => BASE_PATH . '/views',
));

/**
 * Register Database Plugin. Set connection settings.
 */
$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'db.options' => array(
        'driver'   => 'pdo_mysql',
        'host'     => 'localhost',
        'user'     => 'root',
        'password' => '',
        'dbname'   => 'alfaforex',
        'charset'  => 'utf8',
    ),
));

/**
 * Register index controller.
 */
$app->get('/', function (Request $request) use ($app) {
    $products = array();

    $productProperty = new ProductProperty($app['db']);
    $properties = $productProperty->fetchAll();

    $searchParams = $request->get('search');
    if ($searchParams) {
        $products = $productProperty->getProductContainingProperties($searchParams);
    }

    return $app['twig']->render('index.twig', array(
        'properties'   => $properties,
        'products'     => $products,
        'searchParams' => $searchParams,
    ));
});

$app->run();