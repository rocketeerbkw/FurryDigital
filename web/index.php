<?php
use FA\Phalcon\Application;

error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT);

require __DIR__ . '/../app/bootstrap.php';

try
{
    $application = new \FA\Phalcon\Application($di);
    $application->useImplicitView(true);

    $application->bootstrap()->run();
}
catch(\Exception $e)
{
    \FA\Phalcon\ErrorHandler::handle($e, $di);
}
