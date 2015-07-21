<?php

function delete_on_shutdown($file_path) {
    register_shutdown_function(function ($file_path) {
        if (file_exists($file_path)) {
            unlink($file_path);
        }
    }, $file_path);
}
