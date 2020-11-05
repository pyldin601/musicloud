<?php

return [
    'hostname' => getenv('DB_HOSTNAME') ?? 'localhost',
    'database' => getenv('DB_DATABASE') ?? 'musicloud',
    'username' => getenv('DB_USERNAME') ?? 'musicloud',
    'password' => getenv('DB_PASSWORD') ?? 'musicloud'
];
