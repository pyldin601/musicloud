<?php

return [
    'hostname' => $_ENV['DB_HOSTNAME'] ?? 'localhost',
    'database' => $_ENV['DB_DATABASE'] ?? 'musicloud',
    'username' => $_ENV['DB_USERNAME'] ?? 'musicloud',
    'password' => $_ENV['DB_PASSWORD'] ?? 'musicloud'
];
