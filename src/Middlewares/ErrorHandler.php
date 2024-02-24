<?php
namespace App\Middlewares;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Slim\Factory\AppFactory;
use Slim\Views\Twig;

class ErrorHandler {
    private $app_obj = null;

    public function __construct($app) {
        $this->app_obj = $app;
    }

    public function __invoke (
        ServerRequestInterface $req,
        $exception,
        bool $displayErrorDetails,
        bool $logErrors,
        bool $logErrorDetails,
        ?LoggerInterface $logger = null
    ) {
    $params = ['error' => $exception->getMessage()];

    $res = $this->app_obj->getResponseFactory()->createResponse();
    $view = Twig::fromRequest($req);

    return $view->render($res, "404.html", $params);
    }

}
