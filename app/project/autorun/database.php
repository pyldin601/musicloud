<?php
/**
 * Database configuration file
 *
 * Created by PhpStorm
 * User: roman
 * Date: 16.07.15
 * Time: 20:17
 */

use app\core\db\DatabaseConfiguration;
use app\core\etc\Config;
use app\core\modular\Event;

Event::addFilter("database.configure", function (DatabaseConfiguration $configuration) {

    $config = Config::getInstance();

    $dsn = sprintf(
        'pgsql:host=%s;port=5432;dbname=%s',
        $config->get('db.hostname'),
        $config->get('db.database')
    );

    $configuration->setDsnLogin($config->get('db.username'));
    $configuration->setDsnPassword($config->get('db.password'));
    $configuration->setDsnUri($dsn);

    return $configuration;

});
