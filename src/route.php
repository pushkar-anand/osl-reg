<?php
require_once __DIR__ . "/../vendor/autoload.php";

use EasyRoute\Route;
use PhpUseful\EasyHeaders;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

global $logger;

$loader = new FilesystemLoader(__DIR__ . '/../views');

$twig = new Environment($loader, /*[
    'cache' => __DIR__ . '/../cache',
]*/);


try {
    $route = new Route();

    $route->addMatch('GET', '/osd/registration', function () {
        global $twig;
        $twig->display('osd-reg.twig');
    });

    $route->addMatch('POST', '/osd/registration', function () {
        $error = array();
        if (isset($_POST['name']) && isset($_POST['email']) && isset($_POST['phone']) ) {



        } else {
            $error["error"] = "Fill all the fields.";
        }
    });

    $route->execute();

} catch (Exception $e) {
    $logger->critical('Error in routing ->' . $e->getMessage());
    EasyHeaders::server_error();
}
