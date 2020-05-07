<?php
namespace extas\components\jsonrpc;

use extas\components\Item;
use extas\components\jsonrpc\operations\Create;
use extas\components\jsonrpc\operations\Delete;
use extas\components\jsonrpc\operations\Index;
use extas\components\jsonrpc\operations\Update;
use extas\interfaces\jsonrpc\IGenerator;
use extas\interfaces\jsonrpc\operations\IOperation;
use extas\interfaces\plugins\IPlugin;
use extas\interfaces\plugins\IPluginInstallDefault;

/**
 * Class Generator
 *
 * @package extas\components\jsonrpc
 * @author jeyroik@gmail.com
 */
class Generator extends Item implements IGenerator
{
    protected array $result = [];
    protected array $currentProperties;
    protected IPluginInstallDefault $currentPlugin;

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
        return (bool) ($this->config[static::FIELD__ONLY_EDGE] ?? false);
    }

    /**
     * @return string
     */
    public function getFilter(): string
    {
        return $this->config[static::FIELD__FILTER] ?? '';
    }

    /**
     * @param string $filter
     *
     * @return IGenerator
     */
    public function setFilter(string $filter): IGenerator
    {
        $this->config[static::FIELD__FILTER] = $filter;

        return $this;
    }

    /**
     * @param bool $onlyEdge
     *
     * @return IGenerator
     */
    public function setOnlyEdge(bool $onlyEdge): IGenerator
    {
        $this->config[static::FIELD__ONLY_EDGE] = $onlyEdge;

        return $this;
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
     */
    protected function appendCRUDOperations(string $fullName, $plugin, $dotted, $properties): void
    {
        if ($this->isApplicablePlugin($fullName)) {
            $this->currentPlugin = $plugin;
            $this->currentProperties = $properties;
            $this->result['jsonrpc_operations'][] = $this->constructCreate($dotted);
            $this->result['jsonrpc_operations'][] = $this->constructIndex($dotted);
            $this->result['jsonrpc_operations'][] = $this->constructUpdate($dotted);
            $this->result['jsonrpc_operations'][] = $this->constructDelete($dotted);
        }
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
        $constants = $reflection->getConstants();
        $properties = [];

        $this->appendConstantsToProperties($constants, $properties);

        $methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);
        $byNameMethods = array_column($methods, null, 'name');

        $this->fillInPropertySpec($byNameMethods, $properties);

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

    /**
     * @return string
     */
    protected function getSubjectForExtension(): string
    {
        return static::SUBJECT;
    }
}
