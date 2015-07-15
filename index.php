<?php

use app\core\router\Router;

require_once "app/loader.php";


$router = Router::getInstance();

$router->run();


