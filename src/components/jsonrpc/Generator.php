<?php
namespace extas\components\jsonrpc;

use extas\components\Item;
use extas\components\jsonrpc\operations\filters\FilterDefault;
use extas\components\jsonrpc\operations\Index;
use extas\interfaces\jsonrpc\IGenerator;
use extas\interfaces\jsonrpc\operations\IOperation;
use extas\interfaces\plugins\IPluginInstallDefault;

/**
 * Class Generator
 *
 * @package extas\components\jsonrpc
 * @author jeyroik@gmail.com
 */
class Generator extends Item implements IGenerator
{
    /**
     * @param IPluginInstallDefault[] $plugins
     * @param string $path
     *
     * @return bool
     */
    public function generate(array $plugins, string $path): bool
    {
        $result = [
            'name' => '[auto-generated] extas/jsonrpc/operations',
            'jsonrpc__operations' => []
        ];

        foreach ($plugins as $plugin) {
            $properties = $this->generateProperties($plugin);
            $dotted = str_replace(' ', '.', $plugin->getPluginName());

            if ($filter = $this->getFilter()) {
                if(strpos($dotted, $filter) === false) {
                    continue;
                }
            }

            $result['jsonrpc__operations'][] = $this->constructCreate($plugin, $dotted, $properties);
            $result['jsonrpc__operations'][] = $this->constructIndex($plugin, $dotted, $properties);
            $result['jsonrpc__operations'][] = $this->constructUpdate($plugin, $dotted, $properties);
            $result['jsonrpc__operations'][] = $this->constructDelete($plugin, $dotted, $properties);
        }

        file_put_contents($path, json_encode($result));

        return true;
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
     * @param IPluginInstallDefault $plugin
     *
     * @return array
     */
    protected function generateProperties(IPluginInstallDefault $plugin): array
    {
        $reflection = new \ReflectionClass($plugin->getPluginItemClass());
        $constants = $reflection->getConstants();
        $properties = [];

        foreach ($constants as $name => $value) {
            if (strpos($name, 'FIELD') !== false) {
                $properties[$value] = [];
            }
        }

        /**
         * @var $byNameMethods \ReflectionMethod[]
         */
        $methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);
        $byNameMethods = [];

        foreach ($methods as $method) {
            $byNameMethods[$method->getName()] = $method;
        }

        foreach ($properties as $property => $spec) {
            $methodName = 'get' . ucwords(str_replace('_', ' ', $property));
            $type = 'string';
            if (isset($byNameMethods[$methodName])) {
                $returnType = $byNameMethods[$methodName]->getReturnType();
                $type = $returnType ? $returnType->getName() : 'string';
            }
            $properties[$property] = ['type' => $type];
        }

        return $properties;
    }

    /**
     * @param IPluginInstallDefault $plugin
     * @param string $name
     * @param array $properties
     *
     * @return array
     */
    protected function constructCreate(IPluginInstallDefault $plugin, string $name, array $properties)
    {
        return [
            IOperation::FIELD__NAME => $name . '.create',
            IOperation::FIELD__TITLE => 'Create ' . $plugin->getPluginName(),
            IOperation::FIELD__DESCRIPTION => 'Create ' . $plugin->getPluginName(),
            IOperation::FIELD__METHOD => 'create',
            IOperation::FIELD__ITEM_NAME => $name,
            IOperation::FIELD__ITEM_CLASS => $plugin->getPluginItemClass(),
            IOperation::FIELD__ITEM_REPO => $plugin->getPluginRepositoryInterface(),
            IOperation::FIELD__FILTER_CLASS => FilterDefault::class,
            IOperation::FIELD__CLASS => Create::class,
            IOperation::FIELD__SPEC => [
                "request" => [
                    "type" => "object",
                    "properties" => [
                        "data" => [
                            "type" => "object",
                            "properties" => $properties
                        ]
                    ]
                ],
                "response" => [
                    "type" => "object",
                    "properties" => $properties
                ]
            ]
        ];
    }

    /**
     * @param IPluginInstallDefault $plugin
     * @param string $name
     * @param array $properties
     *
     * @return array
     */
    protected function constructIndex(IPluginInstallDefault $plugin, string $name, array $properties)
    {
        return [
            IOperation::FIELD__NAME => $name . '.index',
            IOperation::FIELD__TITLE => 'Index ' . $plugin->getPluginName(),
            IOperation::FIELD__DESCRIPTION => 'Index ' . $plugin->getPluginName(),
            IOperation::FIELD__METHOD => 'index',
            IOperation::FIELD__ITEM_NAME => $name,
            IOperation::FIELD__ITEM_CLASS => $plugin->getPluginItemClass(),
            IOperation::FIELD__ITEM_REPO => $plugin->getPluginRepositoryInterface(),
            IOperation::FIELD__FILTER_CLASS => FilterDefault::class,
            IOperation::FIELD__CLASS => Index::class,
            IOperation::FIELD__SPEC => [
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
                            "properties" => $properties
                        ],
                        "total" => [
                            "type" => "number"
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * @param IPluginInstallDefault $plugin
     * @param string $name
     * @param array $properties
     *
     * @return array
     */
    protected function constructUpdate(IPluginInstallDefault $plugin, string $name, array $properties)
    {
        return [
            IOperation::FIELD__NAME => $name . '.update',
            IOperation::FIELD__TITLE => 'Update ' . $plugin->getPluginName(),
            IOperation::FIELD__DESCRIPTION => 'Update ' . $plugin->getPluginName(),
            IOperation::FIELD__METHOD => 'update',
            IOperation::FIELD__ITEM_NAME => $name,
            IOperation::FIELD__ITEM_CLASS => $plugin->getPluginItemClass(),
            IOperation::FIELD__ITEM_REPO => $plugin->getPluginRepositoryInterface(),
            IOperation::FIELD__FILTER_CLASS => FilterDefault::class,
            IOperation::FIELD__CLASS => Update::class,
            IOperation::FIELD__SPEC => [
                "request" => [
                    "type" => "object",
                    "properties" => [
                        "data" => [
                            "type" => "object",
                            "properties" => $properties
                        ]
                    ]
                ],
                "response" => [
                    "type" => "object",
                    "properties" => $properties
                ]
            ]
        ];
    }

    /**
     * @param IPluginInstallDefault $plugin
     * @param string $name
     * @param array $properties
     *
     * @return array
     */
    protected function constructDelete(IPluginInstallDefault $plugin, string $name, array $properties)
    {
        return [
            IOperation::FIELD__NAME => $name . '.delete',
            IOperation::FIELD__TITLE => 'Delete ' . $plugin->getPluginName(),
            IOperation::FIELD__DESCRIPTION => 'Delete ' . $plugin->getPluginName(),
            IOperation::FIELD__METHOD => 'delete',
            IOperation::FIELD__ITEM_NAME => $name,
            IOperation::FIELD__ITEM_CLASS => $plugin->getPluginItemClass(),
            IOperation::FIELD__ITEM_REPO => $plugin->getPluginRepositoryInterface(),
            IOperation::FIELD__FILTER_CLASS => FilterDefault::class,
            IOperation::FIELD__CLASS => Delete::class,
            IOperation::FIELD__SPEC => [
                "request" => [
                    "type" => "object",
                    "properties" => [
                        "data" => [
                            "type" => "object",
                            "properties" => [
                                "name" => [
                                    "type" => "string"
                                ]
                            ]
                        ]
                    ]
                ],
                "response" => [
                    "type" => "object",
                    "properties" => $properties
                ]
            ]
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
