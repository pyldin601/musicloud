<?php
/**
 * Database configuration file
 *
 * Created by PhpStorm
 * User: roman
 * Date: 16.07.15
 * Time: 20:17
 */

use app\core\db\Database;
use app\core\db\DatabaseConfiguration;
use app\core\etc\Settings;

$settings = Settings::getInstance();
$config = new DatabaseConfiguration();

$config->setDsnUri(     $settings->get("pdo",     "dsn"));
$config->setDsnLogin(   $settings->get("pdo",   "login"));
$config->setDsnPassword($settings->get("pdo", "password"));

Database::configure($config);
