<?php

class Container
{
    private $bindings = [];
    private $instances = [];

    public function bind($abstract, $concrete = null)
    {
        if ($concrete === null) {
            $concrete = $abstract;
        }

        $this->bindings[$abstract] = $concrete;
    }

    public function singleton($abstract, $concrete = null)
    {
        $this->bind($abstract, $concrete);
    }

    public function make($abstract)
    {
        if (isset($this->instances[$abstract])) {
            return $this->instances[$abstract];
        }

        if (!isset($this->bindings[$abstract])) {
            return $this->resolve($abstract);
        }

        $concrete = $this->bindings[$abstract];

        if ($concrete instanceof Closure) {
            $object = $concrete($this);
        } else {
            $object = $this->resolve($concrete);
        }

        $this->instances[$abstract] = $object;

        return $object;
    }

    private function resolve($class)
    {
        $reflector = new ReflectionClass($class);

        if (!$reflector->isInstantiable()) {
            throw new Exception("Class {$class} is not instantiable");
        }

        $constructor = $reflector->getConstructor();

        if ($constructor === null) {
            return new $class;
        }

        $parameters = $constructor->getParameters();
        $dependencies = [];

        foreach ($parameters as $parameter) {
            $dependency = $parameter->getType();

            if ($dependency === null) {
                if ($parameter->isDefaultValueAvailable()) {
                    $dependencies[] = $parameter->getDefaultValue();
                } else {
                    throw new Exception("Cannot resolve parameter {$parameter->name}");
                }
            } else {
                $dependencies[] = $this->make($dependency->getName());
            }
        }

        return $reflector->newInstanceArgs($dependencies);
    }
}
