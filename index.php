<?php
require __DIR__ . "/vendor/autoload.php";

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

use App\Controllers\IndexController;
use App\Controllers\WeatherController;
use App\Middlewares\JsonBodyParser;
use App\Middlewares\TrailingSlash;
use App\Middlewares\ErrorHandler;

// Load de env variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$app = AppFactory::create();
$twig = Twig::create(__DIR__ . "/templates", ['cache' => false]);

$app->addRoutingMiddleware();
$app->add(new TrailingSlash());
$app->add(TwigMiddleware::create($app, $twig));
$app->add(new JsonBodyParser);

$app->get("/", IndexController::class . ":index" );
$app->get("/get/cache", WeatherController::class . ":get_cache" );
$app->get("/get/newest", WeatherController::class . ":get_newest" );

$errorMiddleware = $app->addErrorMiddleware(true, true, true);
$errorMiddleware->setDefaultErrorHandler(new ErrorHandler($app));
$app->run();
