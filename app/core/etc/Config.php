<?php

namespace app\core\etc;

use app\core\injector\Injectable;
use app\lang\singleton\Singleton;
use app\lang\singleton\SingletonInterface;

class Config implements SingletonInterface, Injectable
{
    use Singleton;

    private $config = [];

    protected function __construct()
    {
        $configFiles = scandir(CONFIG_PATH);

        if (!$configFiles) {
            return;
        }

        $this->config = array_reduce($configFiles, function ($acc, $file) {
            list('extension' => $extension, 'filename' => $filename) = pathinfo($file);
            $filepath = CONFIG_PATH . '/' . $file;
            if (is_dir($filepath) || $extension !== 'php') {
                return $acc;
            }
            return array_merge($acc, [$filename => require $filepath]);
        }, []);
    }

    private function makePathFinder(string $path): callable
    {
        $pathParts = explode('.', $path);
        return function (array $config) use ($pathParts) {
            return array_reduce($pathParts, function ($acc, $part) {
                if (!is_null($acc) && is_array($acc)) {
                    return $acc[$part];
                }
                return null;
            }, $config);
        };
    }

    public function config(string $path, $default = null) {
        $find = $this->makePathFinder($path);
        $result = $find($this->config);
        if (!is_null($result)) {
            return $result;
        }
        if (is_null($result) && !is_null($default)) {
            return $default;
        }
        throw new \Exception("Setting '${path}' not found");
    }
}
