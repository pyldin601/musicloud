<?php

$uri = urldecode(parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH));

if ($uri !== '/' && file_exists('.' . $uri)) {
    return false;
}

$_GET['route'] = substr($uri, 1) ?: "index";

require "public/index.php";
