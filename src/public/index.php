<?php
header("Access-Control-Allow-Origin: *");
define('APP__ROOT', getenv('EXTAS__BASE_PATH') ?: __DIR__ . '/../..');
require(APP__ROOT . '/vendor/autoload.php');

if (is_file(APP__ROOT . '/.env')) {
    $dotenv = \Dotenv\Dotenv::create(APP__ROOT . '/');
    $dotenv->load();
}

$app = \extas\components\jsonrpc\App::create();
$app->run();
