<?php
/**
 * Copyright (c) 2017 Roman Lakhtadyr
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace app\core\injector;

use app\core\http\HttpParameter;
use app\lang\option\Option;
use app\lang\singleton\Singleton;
use app\lang\singleton\SingletonInterface;

class Injector implements SingletonInterface
{
    use Singleton;

    public static function run($callable)
    {
        return self::getInstance()->call($callable);
    }

    /**
     * @param $callable
     * @return mixed
     * @throws \Exception
     */
    public function call($callable)
    {
        if (is_string($callable) && class_exists($callable)) {
            $instance = $this->callConstructor($callable);

            if (is_callable($instance)) {
                return $this->call($instance);
            }
        }

        if (is_array($callable) && sizeof($callable) === 2 && is_string($callable[0])) {
            $method = new \ReflectionMethod($callable[0], $callable[1]);
            if (!$method->isStatic()) {
                $instance = $this->callConstructor($callable[0]);
                return $this->call([$instance, $callable[1]]);
            }
        }

        $closure = \Closure::fromCallable($callable);

        $reflection = new \ReflectionFunction($closure);
        $dependencies = $this->injectByFunctionArguments($reflection->getParameters());

        return $closure(...$dependencies);
    }

    /**
     * @param string $className
     * @return mixed
     */
    private function callConstructor(string $className)
    {
        $reflection = new \ReflectionClass($className);
        $constructor = $reflection->getConstructor();

        if (is_null($constructor)) {
            $dependencies = [];
        } else {
            $dependencies = $this->injectByFunctionArguments($constructor->getParameters());
        }

        return new $className(...$dependencies);
    }

    /**
     * @param \ReflectionClass $reflection
     * @return object
     * @throws InjectorException
     */
    public function create(\ReflectionClass $reflection)
    {
        if (!$reflection->implementsInterface(Injectable::class)) {
            throw new InjectorException("Object could not be injected");
        }

        if ($reflection->implementsInterface(SingletonInterface::class)) {
            return $reflection->getMethod("getInstance")->invoke(null);
        }

        return $reflection->newInstance();
    }

    /**
     * @param array $arguments
     * @return array
     */
    public function injectByFunctionArguments(array $arguments)
    {
        $array = [];
        foreach ($arguments as $argument) {
            $array[] = $this->injectByClass($argument);
        }
        return $array;
    }

    /**
     * @param \ReflectionParameter $param
     * @return mixed|object
     * @throws \Exception
     */
    public function injectByClass(\ReflectionParameter $param)
    {
        $reflection = $param->getType() && !$param->getType()->isBuiltin()
            ? new \ReflectionClass($param->getType()->getName())
            : null;

        if (is_null($reflection)) {
            return HttpParameter::getInstance()->getOrError($param->getName());
        } elseif ($reflection->getName() === Option::class) {
            return HttpParameter::getInstance()->get($param->getName());
        }

        return $this->create($reflection);
    }

    public function injectByClassName($class_name)
    {
        $reflection = new \ReflectionClass($class_name);

        if (!$reflection->implementsInterface(Injectable::class)) {
            throw new InjectorException("Object could not be injected");
        }

        if ($reflection->implementsInterface(SingletonInterface::class)) {
            return $reflection->getMethod("getInstance")->invoke(null);
        } else {
            return $reflection->newInstance();
        }
    }

    /**
     * @param array $classes
     * @return array
     * throws \Exception
     */
    public function injectByClassArray(array $classes)
    {
        $array = [];
        foreach ($classes as $class) {
            $array[] = $this->injectByClass($class);
        }
        return $array;
    }
}
