<?php
namespace extas\components\jsonrpc\generators;

use extas\components\jsonrpc\operations\Create;
use extas\components\jsonrpc\operations\Delete;
use extas\components\jsonrpc\operations\Index;
use extas\components\jsonrpc\operations\Update;
use extas\interfaces\jsonrpc\operations\IOperation;
use extas\components\jsonrpc\crawlers\ByPluginInstallDefault as Crawler;
use extas\interfaces\stages\IStageInstallSection;

/**
 * Class ByPluginInstallDefault
 *
 * @package extas\components\jsonrpc
 * @author jeyroik@gmail.com
 */
class ByPluginInstallDefault extends GeneratorDispatcher
{
    public const NAME = 'by.plugin.install.default';

    public const FIELD__FILTER = 'filter';
    public const FIELD__ONLY_EDGE = 'only_edge';
    protected array $currentProperties;
    protected IStageInstallSection $currentPlugin;

    /**
     * @var \ReflectionProperty[]
     */
    protected array $currentPluginProperties = [];

    /**
     * @param array $applicableClasses
     * @throws \Exception
     */
    public function __invoke(array $applicableClasses): void
    {
        if (isset($applicableClasses[Crawler::NAME])) {
            $this->generate($applicableClasses[Crawler::NAME]);
        }
    }

    /**
     * @param IStageInstallSection[] $plugins
     * @return bool
     * @throws \Exception
     */
    public function generate(array $plugins): bool
    {
        foreach ($plugins as $plugin) {
            $this->grabCurrentPluginProperties($plugin);
            $properties = $this->generateProperties($plugin);
            $parts = explode(' ', $this->getCurrentPluginProperty('selfName'));
            $fullName = implode('.', $parts);
            $dotted = $this->getOnlyEdge() ? array_pop($parts) : $fullName;
            $this->appendCRUDOperations($fullName, $plugin, $dotted, $properties);
        }

        $this->exportGeneratedData();

        return true;
    }

    /**
     * @param string $name
     * @return mixed
     * @throws \Exception
     */
    protected function getCurrentPluginProperty(string $name)
    {
        if (!isset($this->currentPluginProperties[$name])) {
            throw new \Exception('Missed current plugin property "' . $name . '"');
        }

        return $this->currentPluginProperties[$name];
    }

    /**
     * @param IStageInstallSection $plugin
     * @throws \ReflectionException
     */
    protected function grabCurrentPluginProperties(IStageInstallSection $plugin): void
    {
        $pluginReflection = new \ReflectionClass($plugin);
        $this->currentPluginProperties = $pluginReflection->getDefaultProperties();
    }

    /**
     * @param string $fullName
     * @param $plugin
     * @param $dotted
     * @param $properties
     * @throws
     */
    protected function appendCRUDOperations(string $fullName, $plugin, $dotted, $properties): void
    {
        $reflection = new \ReflectionClass($this->getCurrentPluginProperty('selfItemClass'));
        $methods = $this->grabMethodsFromComments($reflection);
        $methods = empty($methods) ? ['create', 'index', 'update', 'delete'] : $methods;

        if ($this->isApplicableOperation($fullName)) {
            $this->currentPlugin = $plugin;
            $this->currentProperties = $properties;
            foreach ($methods as $method) {
                $methodConstruct = 'construct' . ucfirst($method);
                $this->addOperation($this->$methodConstruct($dotted));
            }
        }
    }

    /**
     * @param \ReflectionClass $reflection
     * @return array
     */
    protected function grabMethodsFromComments(\ReflectionClass $reflection): array
    {
        $comment = $reflection->getDocComment();
        preg_match_all('/@jsonrpc_method\s(\S+)/', $comment, $matches);

        return empty($matches[1]) ? [] : $matches[1];
    }

    /**
     * @param IStageInstallSection $plugin
     *
     * @return array
     * @throws
     */
    protected function generateProperties(IStageInstallSection $plugin): array
    {
        $reflection = new \ReflectionClass($this->getCurrentPluginProperty('selfRepositoryClass'));
        $properties = $this->grabPropertiesFromComments($reflection);

        if (empty($properties)) {
            $constants = $reflection->getConstants();
            $this->appendConstantsToProperties($constants, $properties);
            $methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);
            $byNameMethods = array_column($methods, null, 'name');

            $this->fillInPropertySpec($byNameMethods, $properties);
        }

        return $properties;
    }

    /**
     * @param \ReflectionClass $reflection
     * @return array
     */
    protected function grabPropertiesFromComments(\ReflectionClass $reflection): array
    {
        $comment = $reflection->getDocComment();
        preg_match_all('/@jsonrpc_field\s(\S+):(\S+)/', $comment, $matches);
        $properties = [];

        if (!empty($matches[1])) {
            foreach ($matches[1] as $index => $propertyName) {
                $properties[$propertyName] = ['type' => $matches[2][$index]];
            }
        }

        return $properties;
    }

    /**
     * @param string $property
     * @param array $byNameMethods
     * @return string
     */
    protected function generatePropertyType(string $property, array $byNameMethods): string
    {
        $methodName = 'get' . ucwords(str_replace('_', ' ', $property));
        $type = 'string';
        if (isset($byNameMethods[$methodName])) {
            $returnType = $byNameMethods[$methodName]->getReturnType();
            $type = $returnType ? $returnType->getName() : 'string';
        }

        return $type;
    }

    /**
     * @param array $byNameMethods
     * @param array $properties
     */
    protected function fillInPropertySpec(array $byNameMethods, array &$properties): void
    {
        foreach ($properties as $property => $spec) {
            $properties[$property] = ['type' => $this->generatePropertyType($property, $byNameMethods)];
        }
    }

    /**
     * @param array $constants
     * @param array $properties
     */
    protected function appendConstantsToProperties(array $constants, array &$properties): void
    {
        foreach ($constants as $name => $value) {
            if (strpos($name, 'FIELD') !== false) {
                $properties[$value] = [];
            }
        }
    }

    /**
     * @param string $name
     * @return array
     * @throws \Exception
     */
    protected function constructCreate(string $name)
    {
        return $this->constructCRUDOperation('create', $name, Create::class);
    }

    /**
     * @param string $name
     * @return array
     * @throws \Exception
     */
    protected function constructIndex(string $name)
    {
        return $this->constructCRUDOperation('index', $name, Index::class, [
            "request" => [
                "type" => "object",
                "properties" => [
                    "limit" => [
                        "type" => "number"
                    ]
                ]
            ],
            "response" => [
                "type" => "object",
                "properties" => [
                    "items" => [
                        "type" => "object",
                        "properties" => $this->currentProperties
                    ],
                    "total" => [
                        "type" => "number"
                    ]
                ]
            ]
        ]);
    }

    /**
     * @param string $name
     * @return array
     * @throws \Exception
     */
    protected function constructUpdate(string $name)
    {
        return $this->constructCRUDOperation('update', $name, Update::class);
    }

    /**
     * @param string $name
     * @return array
     * @throws \Exception
     */
    protected function constructDelete(string $name)
    {
        return $this->constructCRUDOperation('delete', $name, Delete::class);
    }

    /**
     * @param string $crudName
     * @param string $operationName
     * @param string $operationClass
     * @param array $specs
     * @return array
     * @throws \Exception
     */
    protected function constructCRUDOperation(
        string $crudName,
        string $operationName,
        string $operationClass,
        array $specs = []
    ): array
    {
        $specs = $specs ?: [
            "request" => [
                "type" => "object",
                "properties" => [
                    "data" => [
                        "type" => "object",
                        "properties" => $this->currentProperties
                    ]
                ]
            ],
            "response" => ["type" => "object", "properties" => $this->currentProperties]
        ];

        return [
            IOperation::FIELD__NAME => $operationName . '.' . $crudName,
            IOperation::FIELD__TITLE => $this->getHighName($crudName),
            IOperation::FIELD__DESCRIPTION => $this->getHighName($crudName),
            IOperation::FIELD__METHOD => $crudName,
            IOperation::FIELD__ITEM_NAME => $operationName,
            IOperation::FIELD__ITEM_CLASS => $this->getCurrentPluginProperty('selfItemClass'),
            IOperation::FIELD__ITEM_REPO => $this->getCurrentPluginProperty('selfRepositoryClass'),
            IOperation::FIELD__CLASS => $operationClass,
            IOperation::FIELD__SPEC => $specs
        ];
    }

    /**
     * @param string $crudName
     * @return string
     * @throws \Exception
     */
    protected function getHighName(string $crudName): string
    {
        return ucfirst($crudName) . ' ' . $this->getCurrentPluginProperty('selfName');
    }
}
