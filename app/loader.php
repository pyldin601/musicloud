<?php

use app\core\http\HttpServer;
use app\core\injector\Injector;
use app\core\view\JsonResponse;
use app\core\view\TinyView;
use app\lang\Arrays;

require_once "constants.php";
require_once "core/shortcuts.php";

const STATIC_CLASS_INIT_METHOD = "class_init";

// Registering base class loader
spl_autoload_register(function ($class_name) {
    $filename = str_replace("\\", "/", $class_name) . '.php';
    if (file_exists($filename)) {
        require_once $filename;
        static_class_init($class_name);
    }
});

// Registering additional class loader for libraries
spl_autoload_register(function ($class_name) {
    $filename = LIBRARIES_PATH . str_replace("\\", "/", $class_name) . '.php';
    if (file_exists($filename)) {
        require_once $filename;
        static_class_init($class_name);
    }
});

// Set global exception handler
set_exception_handler(function (Exception $exception) {
    http_response_code(400);
    if (JsonResponse::hasInstance()) {
        JsonResponse::getInstance()->write(array(
            "error"         => Arrays::last(explode("\\", get_class($exception))),
            "message"       => $exception->getMessage(),
//            "description"   => $exception->getTraceAsString()
        ));
    } else {
        TinyView::show("error.tmpl", array(
            "title"         => Arrays::last(explode("\\", get_class($exception))),
            "message"       => $exception->getMessage(),
//            "description"   => $exception->getTraceAsString()
        ));
    }
});

//if (resource(HttpServer::class)->getContentType() === "application/json") {
//    JsonResponse::getInstance();
//}

//set_error_handler(function ($err_no, $err_str, $err_file, $err_line, array $err_context) {
//    TinyView::show("error.tmpl", array(
//        "title"         => $err_str,
//        "message"       => "At line " . $err_line,
//        "description"   => $err_file
//    ));
//});
//register_shutdown_function(function () {});


// Scan autorun directory for executable scripts
foreach (scandir(AUTORUN_SCRIPTS_PATH) as $file) {
    if ($file == "." || $file == "..")
        continue;
    require_once AUTORUN_SCRIPTS_PATH . $file;
}



function static_class_init($class_name) {
    if (class_exists($class_name) && method_exists($class_name, STATIC_CLASS_INIT_METHOD)) {
        $ref = new ReflectionMethod($class_name, STATIC_CLASS_INIT_METHOD);
        if ($ref->isStatic()) {
            Injector::run($ref);
        }
    }
}

function resource($class_name) {
    return Injector::getInstance()->injectByClassName($class_name);
}

