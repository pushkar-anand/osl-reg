<?php
require_once __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . '/logger.php';

use EasyRoute\Route;
use PhpUseful\EasyHeaders;
use PhpUseful\Functions;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;


$loader = new FilesystemLoader(__DIR__ . '/../views');

$twig = new Environment($loader /*[
    'cache' => __DIR__ . '/../cache',
]*/);


try {
    $route = new Route();

    $route->addMatch('GET', '/', function () {
        global $twig;
        $twig->display('osd-reg.twig');
    });


    $route->addMatch('GET', '/osd/registration', function () {
        global $twig;
        $twig->display('osd-reg.twig');
    });

    $route->addMatch('POST', '/osd/registration', function () {
        global $twig;
        $val = array();
        $error = array();
        if (isset($_POST['name']) && isset($_POST['email']) && isset($_POST['phone'])  && isset($_POST['package'])) {

            $name = Functions::escapeInput($_POST['name']);
            $email = Functions::escapeInput($_POST['email']);
            $phone = Functions::escapeInput($_POST['phone']);
            $package = Functions::escapeInput($_POST['package']);

            $val["name"] = $name;
            $val["email"] = $email;
            $val["phone"] = $phone;


            try {
                $register = new Registration($name, $email, $phone, $package);
                $register->saveDetails();
            }catch (Exception $exception) {
                $error["email"] = $exception->getMessage();
            }

        } else {
            $error["error"] = "Fill all the fields.";
        }

        getLogger()->debug('DEBUG ERROR', $error);

        if (count($error) > 0) {
            getLogger()->debug('ERROR EXISTS.');
            $val["error"] = $error;
            echo $twig->render('osd-reg.twig', $val);
        } else {
            getLogger()->debug('NO ERROR REDIRECTING');
            if ($package === 'package-1') {
                EasyHeaders::redirect('/osd/confirm/package1');
            } else {
                EasyHeaders::redirect('/osd/confirm/package2');
            }

        }
    });

    $route->addMatch('GET', '/osd/confirm/package1', function () {
        EasyHeaders::redirect('');
    });

    $route->addMatch('GET', '/osd/confirm/package2', function () {
        EasyHeaders::redirect('');
    });

    $route->execute();

} catch (Exception $e) {
    getLogger()->critical('Error in routing ->' . $e->getMessage());
    EasyHeaders::server_error();
}
