<?php
namespace extas\components\jsonrpc\generators;

use extas\components\jsonrpc\operations\Create;
use extas\components\jsonrpc\operations\Delete;
use extas\components\jsonrpc\operations\Index;
use extas\components\jsonrpc\operations\Update;
use extas\components\plugins\jsonrpc\PluginDefaultArguments;
use extas\interfaces\jsonrpc\operations\IOperation;
use extas\interfaces\plugins\IPlugin;
use extas\interfaces\plugins\IPluginInstallDefault;
use extas\components\jsonrpc\crawlers\ByPluginInstallDefault as Crawler;

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

    protected array $result = [];
    protected array $currentProperties;
    protected IPluginInstallDefault $currentPlugin;

    /**
     * @param array $applicableClasses
     */
    public function __invoke(array $applicableClasses): void
    {
        $path = $this->getInput()->getOption(PluginDefaultArguments::OPTION__SPECS_PATH);

        if (isset($applicableClasses[Crawler::NAME])) {
            $this->generate($applicableClasses[Crawler::NAME], $path);
        }
    }

    /**
     * @param IPluginInstallDefault[]|IPlugin[] $plugins
     * @param string $path
     *
     * @return bool
     */
    public function generate(array $plugins, string $path): bool
    {
        $this->result = [
            'name' => '[auto-generated] extas/jsonrpc/operations',
            'jsonrpc_operations' => []
        ];

        foreach ($plugins as $plugin) {
            $properties = $this->generateProperties($plugin);
            $parts = explode(' ', $plugin->getPluginName());
            $fullName = implode('.', $parts);
            $dotted = $this->getOnlyEdge() ? array_pop($parts) : $fullName;
            $this->appendCRUDOperations($fullName, $plugin, $dotted, $properties);
        }

        $this->exportGeneratedData($path);

        return true;
    }

    /**
     * @return bool
     */
    public function getOnlyEdge(): bool
    {
        return (bool) $this->getInput()->getOption('only-edge');
    }

    /**
     * @return string
     */
    public function getFilter(): string
    {
        return $this->getInput()->getOption('filter');
    }

    /**
     * @param string $path
     */
    protected function exportGeneratedData(string $path): void
    {
        file_put_contents($path, json_encode($this->result));
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
        $reflection = new \ReflectionClass($plugin->getPluginItemClass());
        $methods = $this->grabMethodsFromComments($reflection);
        $methods = empty($methods) ? ['create', 'index', 'update', 'delete'] : $methods;

        if ($this->isApplicablePlugin($fullName)) {
            $this->currentPlugin = $plugin;
            $this->currentProperties = $properties;
            foreach ($methods as $method) {
                $methodConstruct = 'construct' . ucfirst($method);
                $this->result['jsonrpc_operations'][] = $this->$methodConstruct($dotted);
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
     * @param string $fullName
     * @return bool
     */
    protected function isApplicablePlugin(string $fullName): bool
    {
        $filter = $this->getFilter();

        if($filter && (strpos($fullName, $filter) === false)) {
            return false;
        }

        return true;
    }

    /**
     * @param IPluginInstallDefault $plugin
     *
     * @return array
     * @throws
     */
    protected function generateProperties(IPluginInstallDefault $plugin): array
    {
        $reflection = new \ReflectionClass($plugin->getPluginItemClass());
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
     *
     * @return array
     */
    protected function constructCreate(string $name)
    {
        return $this->constructCRUDOperation('create', $name, Create::class);
    }

    /**
     * @param string $name
     *
     * @return array
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
     *
     * @return array
     */
    protected function constructUpdate(string $name)
    {
        return $this->constructCRUDOperation('update', $name, Update::class);
    }

    /**
     * @param string $name
     *
     * @return array
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
            IOperation::FIELD__TITLE => ucfirst($crudName) . ' ' . $this->currentPlugin->getPluginName(),
            IOperation::FIELD__DESCRIPTION => ucfirst($crudName) . ' ' . $this->currentPlugin->getPluginName(),
            IOperation::FIELD__METHOD => $crudName,
            IOperation::FIELD__ITEM_NAME => $operationName,
            IOperation::FIELD__ITEM_CLASS => $this->currentPlugin->getPluginItemClass(),
            IOperation::FIELD__ITEM_REPO => $this->currentPlugin->getPluginRepositoryInterface(),
            IOperation::FIELD__CLASS => $operationClass,
            IOperation::FIELD__SPEC => $specs
        ];
    }
}
