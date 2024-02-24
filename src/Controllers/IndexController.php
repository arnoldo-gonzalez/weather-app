<?php
namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class IndexController {
    function index(Request $req, Response $res, array $args) {
        $view = Twig::fromRequest($req);
        return $view->render($res, "index.html", []);
    }
}
