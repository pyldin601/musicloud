<?php
/**
 * Created by PhpStorm.
 * User: roman
 * Date: 24.02.15
 * Time: 22:23
 */

namespace app\core\injector;



use app\core\etc\Settings;
use app\core\http\HttpParameter;
use app\lang\option\Option;
use app\lang\singleton\Singleton;
use app\lang\singleton\SingletonInterface;

class Injector implements SingletonInterface {

    use Singleton;

    public static function run($callable) {

        return self::getInstance()->call($callable);

    }

    /**
     * @param $callable
     * @return mixed
     * @throws \Exception
     */
    public function call($callable) {

        if ($callable instanceof \ReflectionFunction) {

            return $callable->invokeArgs(
                $this->injectByFunctionArguments($callable->getParameters()));

        } else if ($callable instanceOf \ReflectionMethod) {

            return $callable->invokeArgs(null,
                $this->injectByFunctionArguments($callable->getParameters()));

        } else if (is_callable($callable) && is_array($callable)) {

            $reflection = new \ReflectionClass($callable[0]);
            $method = $reflection->getMethod($callable[1]);

            return $method->invokeArgs($callable[0],
                $this->injectByFunctionArguments($method->getParameters()));

        } else if (is_callable($callable)) {

            $reflection = new \ReflectionFunction($callable);

            return $this->call($reflection);

        } else {

            throw new InjectorException("Wrong type of argument");

        }

    }

    /**
     * @param \ReflectionClass $reflection
     * @return object
     * @throws InjectorException
     */
    public function create(\ReflectionClass $reflection) {

        $constructor = $reflection->getConstructor();

        $dependencies = $this->injectByFunctionArguments(
            $constructor->getParameters());

        if (!$reflection->implementsInterface(Injectable::class)) {
            throw new InjectorException("Object could not be injected");
        }

        if ($reflection->implementsInterface(SingletonInterface::class)) {
            return $reflection->getMethod("getInstance")->invokeArgs(null, $dependencies);
        } else {
            return $reflection->newInstanceArgs($dependencies);
        }


    }

    /**
     * @param array $arguments
     * @return array
     */
    public function injectByFunctionArguments(array $arguments) {
        $array = [];
        foreach ($arguments as $argument) {
            $array[] = $this->injectByClass($argument);
        }
        return $array;
    }

    /**
     * @param \ReflectionParameter $class
     * @return mixed|object
     * @throws \Exception
     */
    public function injectByClass($class) {

        $reflection = $class->getClass();

        if (is_null($reflection)) {

            return HttpParameter::getInstance()->getOrError($class->getName());

        } else if ($reflection->getName() === Option::class) {

            return HttpParameter::getInstance()->get($class->getName());

        }

        return $this->create($reflection);

    }

    public function injectByClassName($class_name) {

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
    public function injectByClassArray(array $classes) {
        $array = [];
        foreach ($classes as $class) {
            $array[] = $this->injectByClass($class);
        }
        return $array;
    }

} 