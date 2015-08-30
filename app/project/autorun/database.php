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
use app\core\db\DatabaseConnection;
use app\core\etc\Settings;
use app\core\modular\Event;

Event::addFilter(DatabaseConnection::FILTER_DB_CONFIGURE,
    function (DatabaseConfiguration $config) {

        $settings = Settings::getInstance();

        $config->setDsnLogin($settings->get("pdo", "login"));
        $config->setDsnPassword($settings->get("pdo", "password"));
        $config->setDsnUri($settings->get("pdo", "dsn"));

        return $config;

    }
);

