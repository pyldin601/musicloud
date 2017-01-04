<?php

use app\core\router\Router;
use josegonzalez\Dotenv\Loader;

if (empty($_SERVER['PHP_AUTH_USER']) || empty($_SERVER['PHP_AUTH_PW']) || $_SERVER['PHP_AUTH_USER'] != "guest" || $_SERVER['PHP_AUTH_PW'] != "please") {
    header('WWW-Authenticate: Basic realm="Site is under construction"');
    header('HTTP/1.0 401 Unauthorized');
    echo "Sorry, you haven't access to this resource.";
    exit;
}

$used_before = memory_get_usage();

require_once "../vendor/autoload.php";
require_once "../app/loader.php";

if (file_exists(__DIR__ . '/../.env')) {
    $Loader = new josegonzalez\Dotenv\Loader(__DIR__ . '/../.env');
    $Loader->parse();
    $Loader->toEnv();
}

$router = Router::getInstance();

$router->run();

$used_after = memory_get_usage();

\app\core\logging\Logger::printf("Memory used: %d", $used_after - $used_before);
