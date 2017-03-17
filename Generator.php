<?php

namespace b2r\Component\Composition;

use ReflectionClass;
use ReflectionMethod;
use b2r\Component\Twig\Twig;

/**
 * Easy to generate composition php source
 */
class Generator
{
    /**
     * @var [string $aliasName => string $methodName]
     */
    private $aliases = [];

    /**
     * @var [string $excludeMethodName => true]
     */
    private $excludes = [];

    private $params = [
        'classType'  => 'class', // class|trait
        'namespace'  => null,    // Namespace name
        'target'     => null,    // Target class name
        'targetName' => null,    // Target class short name
        'property'   => null,    // Property name
    ];

    /**
     * @var \ReflectionClass
     */
    private $ref = null;

    public function __call($name, $arguments)
    {
        foreach (['set', 'add'] as $prefix) {
            $method = $prefix . $name;
            if (method_exists($this, $method)) {
                return $this->$method(...$arguments);
            }
        }
    }

    public function __toString()
    {
        return $this->generate();
    }

    public function addAlias(string $alias, string $method): self
    {
        $this->aliases[$alias] = $method;
        return $this;
    }

    public function addAliases(array $aliases): self
    {
        foreach ($aliases as $alias => $method) {
            $this->addAlias($alias, $method);
        }
        return $this;
    }

    public function addExclude(string $name): self
    {
        $this->excludes[strtolower($name)]  = true;
        return $this;
    }

    public function addExcludes(array $names): self
    {
        foreach ($names as $name) {
            $this->addExclude($name);
        }
        return $this;
    }

    public function asClass(): self
    {
        $this->params['classType'] = 'class';
        return $this;
    }

    public function asTrait(): self
    {
        $this->params['classType'] = 'trait';
        return $this;
    }

    public function generate(): string
    {
        $twig = new Twig(__DIR__);
        $this->params['methods'] = $this->getMethods();
        $this->params['aliases'] = $this->getAliases();
        return $twig->template('template.twig')->render($this->params);
    }

    public function save(string $filename): self
    {
        file_put_contents($filename, $this->generate());
        return $this;
    }

    public function setName(string $name): self
    {
        $this->params['name'] = $name;
        return $this;
    }

    public function setNamespace(string $namespace): self
    {
        $this->params['namespace'] = $namespace;
        return $this;
    }

    public function setProperty(string $property): self
    {
        $this->params['property'] = $property;
        $this->params['getter'] = 'get' . ucfirst($property);
        $this->params['setter'] = 'set' . ucfirst($property);
        return $this;
    }

    public function setTarget(string $class): self
    {
        $this->ref = new ReflectionClass($class);
        $this->params['target'] = $this->ref->getName();
        $this->params['targetName'] = $this->ref->getShortName();
        return $this;
    }

    private function getAliases(): array
    {
        if (!$this->aliases) {
            return [];
        }

        $ref = $this->ref;
        $methods = [];
        foreach ($this->aliases as $alias => $method) {
            $method = $ref->getMethod($method);
            $name = $method->getName();
            $methods[$name] = [
                'alias' => $alias,
                'name'  => $name,
                'argc'  => $method->getNumberOfParameters(),
            ];
        }
        ksort($methods);
        return $methods;
    }

    private function getMethods(): array
    {
        $methods = [];
        foreach ($this->ref->getMethods() as $method) {
            if ($this->isPublished($method)) {
                $name = $method->getName();
                $methods[$name] = [
                    'name' => $name,
                    'argc' => $method->getNumberOfParameters(),
                ];
            }
        }
        ksort($methods);
        return $methods;
    }

    private function isPublished($method): bool
    {
        $name = strtolower($method->getName());
        return $method->isPublic() && $name[0] !== '_' && !array_key_exists($name, $this->excludes);
    }
}
