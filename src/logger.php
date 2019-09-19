<?php
require_once __DIR__ . "/../vendor/autoload.php";

use Monolog\Handler\NativeMailerHandler;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

function getLogger()
{

    $logFileDebug = __DIR__ . "/../logs/debug.log";
    $logFileError = __DIR__ . "/../logs/error.log";
    $logFileInfo = __DIR__ . "/../logs/info.log";

    $logger = new Logger('Codezi.app Logger');

    try {
        $logger->pushHandler(new StreamHandler($logFileDebug, Logger::DEBUG));
        $logger->pushHandler(new StreamHandler($logFileError, Logger::ERROR));
        $logger->pushHandler(new StreamHandler($logFileInfo, Logger::INFO));
        $logger->pushHandler(new NativeMailerHandler(TO_EMAIL,
            SUBJECT,
            FROM_EMAIL,
            Logger::CRITICAL));

        $logger->info('Logger is now ready');

    } catch (Exception $e) {
        error_log("Error in setting up logger." . $e->getMessage());
    }

    return $logger;
}
