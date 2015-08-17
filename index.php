<?php

use app\core\router\Router;

$used_before = memory_get_usage();

require_once "app/loader.php";


$router = Router::getInstance();

$router->run();

$used_after = memory_get_usage();

\app\core\logging\Logger::printf("Memory used: %d", $used_after - $used_before);