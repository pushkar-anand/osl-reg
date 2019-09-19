<?php
require_once __DIR__ . "/../vendor/autoload.php";

use EasyRoute\Route;
use PhpUseful\EasyHeaders;
use PhpUseful\Functions;
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
        global $twig;
        $error = array();
        if (isset($_POST['name']) && isset($_POST['email']) && isset($_POST['phone'])  && isset($_POST['package'])) {

            $name = Functions::escapeInput($_POST['name']);
            $email = Functions::escapeInput($_POST['email']);
            $phone = Functions::escapeInput($_POST['phone']);
            $package = Functions::escapeInput($_POST['package']);


            try {
                $register = new Registration($name, $email, $phone, $package);
                $register->saveDetails();
            }catch (Exception $exception) {
                $error["email"] = $exception->getMessage();
            }

        } else {
            $error["error"] = "Fill all the fields.";
        }

        if (empty($error)) {
            echo $twig->render('osd-reg.twig', $error);
        } else {
            EasyHeaders::redirect('/osd/confirm');
        }
    });

    $route->addMatch('GET', '/osd/confirm', function () {

    });

    $route->execute();

} catch (Exception $e) {
    $logger->critical('Error in routing ->' . $e->getMessage());
    EasyHeaders::server_error();
}
