<?php

use app\core\router\Router;
use josegonzalez\Dotenv\Loader;
use app\core\logging\Logger;

define('APP_ROOT_DIR', realpath('..'));
define('APP_LIB_DIR', APP_ROOT_DIR . '/app/lib');

if (!file_exists(APP_ROOT_DIR . '/vendor/autoload.php')) {
    http_response_code(500);
    echo '<h1>"vendor" directory not found!</h1>';
    exit();
}

if (empty($_SERVER['PHP_AUTH_USER']) || empty($_SERVER['PHP_AUTH_PW']) || $_SERVER['PHP_AUTH_USER'] != "guest" || $_SERVER['PHP_AUTH_PW'] != "please") {
    header('WWW-Authenticate: Basic realm="Site is under construction"');
    header('HTTP/1.0 401 Unauthorized');
    echo "Sorry, you haven't access to this resource.";
    exit;
}

$used_before = memory_get_usage();

require_once "../app/loader.php";
require_once "../vendor/autoload.php";

if (file_exists(APP_ROOT_DIR . '/.env')) {
    $loader = new Loader(APP_ROOT_DIR . '/.env');
    $loader->parse();
    $loader->toEnv(true);
}

$router = Router::getInstance();

$router->run();

$used_after = memory_get_usage();

Logger::printf("Memory used: %d", $used_after - $used_before);
