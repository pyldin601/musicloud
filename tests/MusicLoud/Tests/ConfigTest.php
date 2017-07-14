<?php

namespace MusicLoud\Tests;

use app\core\etc\Config;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    public function testGetSettings()
    {
        $config = Config::getInstance();
        $setting = $config->get('db.hostname');
        $this->assertEquals('localhost', $setting);
    }

    /**
     * @expectedException \Exception
     */
    public function testSettingsNotFound()
    {
        $config = Config::getInstance();
        $config->get('foo.bar');
    }
}
