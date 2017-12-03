<?php

namespace App\Container;

use ReflectionClass;
use ReflectionMethod;
use ReflectionParameter;
use App\Container\Exception\NotFoundException;

class Container
{
    /**
     * Container items
     *
     * @var array
     */
    protected $items = [];

    /**
     * Set item in a container (as singleton)
     *
     * @param string   $name
     * @param callable $closure
     */
    public function share(string $name, callable $closure)
    {
        $this->items[$name] = function () use ($closure) {
            static $resolved;

            if (!$resolved) {
                $resolved = $closure($this);
            }

            return $resolved;
        };
    }

    /**
     * Set item in a container
     *
     * @param string   $name
     * @param callable $closure
     */
    public function set(string $name, callable $closure)
    {
        $this->items[$name] = $closure;
    }

    /**
     * Get item from container
     *
     * @param  string $name
     * @return mixed
     * @throws NotFoundException
     */
    public function get(string $name)
    {
        if ($this->has($name)) {
            return $this->items[$name]($this);
        }

        return $this->autowire($name);
    }

    /**
     * Is there an item in the container
     *
     * @param  string $name
     * @return bool
     */
    public function has(string $name)
    {
        return isset($this->items[$name]);
    }

    /**
     * Autowiring
     *
     * @param  $name
     * @return mixed
     * @throws NotFoundException
     */
    public function autowire($name)
    {
        if (!class_exists($name)) {
            throw new NotFoundException;
        }

        $reflector = $this->getReflector($name);

        if (!$reflector->isInstantiable()) {
            throw new NotFoundException;
        }

        if ($constructor = $reflector->getConstructor()) {
            return $reflector->newInstanceArgs(
                $this->getReflectorConstructorDependencies($constructor)
            );
        }

        return new $name();
    }

    /**
     * Magic get
     *
     * @param  string $name
     * @return mixed
     * @throws NotFoundException
     */
    public function __get(string $name)
    {
        return $this->get($name);
    }

    /**
     * Get Reflection Class
     *
     * @param $class
     * @return ReflectionClass
     */
    protected function getReflector($class)
    {
        return new ReflectionClass($class);
    }

    /**
     * Get constructor dependencies
     *
     * @param  ReflectionMethod $constructor
     * @return array
     */
    protected function getReflectorConstructorDependencies(ReflectionMethod $constructor)
    {
        return array_map(function (ReflectionParameter $dependence) {
            return $this->resolveReflectedDependence($dependence);
        }, $constructor->getParameters());
    }

    /**
     * Resolve reflected dependence
     *
     * @param  ReflectionParameter $dependence
     * @return mixed
     * @throws NotFoundException
     */
    protected function resolveReflectedDependence(ReflectionParameter $dependence)
    {
        if (null == $dependence->getClass()) {
            throw new NotFoundException;
        }

        return $this->get($dependence->getClass()->getName());
    }
}
