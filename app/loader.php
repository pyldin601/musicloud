<?php

use app\core\view\TinyView;

require_once "constants.php";
require_once "core/shortcuts.php";

// Registering class loader
spl_autoload_register(function ($class_name) {
    $filename = str_replace("\\", "/", $class_name) . '.php';
    if (file_exists($filename)) {
        require_once $filename;
    }
});

// Set global exception handler
set_exception_handler(function (Exception $exception) {
    http_response_code(400);
    TinyView::show("error.tmpl", array(
        "title"         => get_class($exception),
        "message"       => $exception->getMessage(),
        "description"   => $exception->getTraceAsString()
    ));
});

//set_error_handler(function () {
//    die("OOPS!");
//});

// Scan autorun directory for executable scripts
foreach (scandir(AUTORUN_SCRIPTS_PATH) as $file) {
    if ($file == "." || $file == "..")
        continue;
    require_once AUTORUN_SCRIPTS_PATH . $file;
}

