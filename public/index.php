<?php

use Slim\Factory\AppFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
// Twig
use Slim\Views\Twig;
//use Slim\Views\TwigMiddleware;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/poo/Usuario.php';
require_once __DIR__ . '/../src/poo/Auto.php';
require_once __DIR__ . '/../src/poo/MW.php';

$app = AppFactory::create();

$twig = Twig::create('../src/views', ['cache' => false]);

//$app->add(TwigMiddleware::create($app, $twig));

$app->get('/front-end-login', function(Request $request, Response $response, array $args){
    $view = Twig::fromRequest($request);
    return $view->render($response, 'login.html', ['titulo' => 'Login']);
});

$app->get('/front-end-registro', function(Request $request, Response $response, array $args){
    $view = Twig::fromRequest($request);
    return $view->render($response, 'registro.html', ['titulo' => 'Registro']);
});

$app->get('/front-end-principal', function(Request $request, Response $response, array $args){
    $view = Twig::fromRequest($request);
    return $view->render($response, 'principal.php', ['titulo' => 'Principal']);
});

$app->post('/usuarios', \Usuario::class . ':AltaUsuario');
// ->add(\MW::class . '::VerificarCorreoBD')
// ->add(\MW::class . '::VerificarCorreoClaveVacios')
// ->add(\MW::class . ':ValidarParametrosUsuario');

$app->get('[/]', \Usuario::class . ':ListadoUsuarios');
// ->add(\MW::class . '::AccedePropietarioUsuario')
// ->add(\MW::class . ':AccedeEmpleadoUsuario')
// ->add(\MW::class . ':AccedeEncargadoUsuario');

$app->post('[/]', \Auto::class . ':AltaAuto')
->add(\MW::class . '::VerificarPrecioColor');

$app->get('/autos', \Auto::class . ':ListadoAutos');
// ->add(\MW::class . '::AccedePropietarioAuto')
// ->add(\MW::class . '::AccedeEmpleadoAuto')
// ->add(\MW::class . ':AccedeEncargadoAuto');

$app->post('/login', \Usuario::class . ':Login');
// ->add(\MW::class . '::VerificarParametrosBD')
// ->add(\MW::class . '::VerificarCorreoClaveVacios')
// ->add(\MW::class . ':ValidarParametrosUsuario');

$app->get('/login', \Usuario::class . ':VerificarJWT');

$app->delete('[/]', \Auto::class . '::BorrarAuto');
// ->add(\MW::class . '::VerificarPropietario')
// ->add(\MW::class . ':VerificarToken');

$app->put('[/]', \Auto::class . '::ModificarAuto');
// ->add(\MW::class . ':VerificarEncargado')
// ->add(\MW::class . ':VerificarToken');


$app->run();
?>