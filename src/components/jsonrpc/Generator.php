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
            'jsonrpc__operations' => []
        ];

        foreach ($plugins as $plugin) {
            $reflection = new \ReflectionClass($plugin->getPluginItemClass());
            $constants = $reflection->getConstants();
            $attributes = [];

            foreach ($constants as $name => $value) {
                if (strpos($name, 'FIELD') !== false) {
                    $attributes[$value] = [
                        "type" => "string"
                    ];
                }
            }
            $dotted = str_replace('_', '.', $plugin->getPluginSection());
            $result['jsonrpc__operations'][] = $this->constructCreate($plugin, $dotted, $attributes);
            $result['jsonrpc__operations'][] = $this->constructIndex($plugin, $dotted, $attributes);
            $result['jsonrpc__operations'][] = $this->constructUpdate($plugin, $dotted, $attributes);
            $result['jsonrpc__operations'][] = $this->constructDelete($plugin, $dotted, $attributes);
        }

        file_put_contents($path, json_encode($result));

        return true;
    }

    /**
     * @param IPluginInstallDefault $plugin
     * @param string $name
     * @param array $attributes
     *
     * @return array
     */
    protected function constructCreate(IPluginInstallDefault $plugin, string $name, array $attributes)
    {
        return [
            IOperation::FIELD__NAME => $name . '.create',
            IOperation::FIELD__TITLE => 'Create ' . $plugin->getPluginName(),
            IOperation::FIELD__DESCRIPTION => 'Create ' . $plugin->getPluginName(),
            IOperation::FIELD__METHOD => 'create',
            IOperation::FIELD__ITEM_NAME => $name,
            IOperation::FIELD__ITEM_CLASS => $plugin->getPluginItemClass(),
            IOperation::FIELD__ITEM_REPO => $plugin->getPluginRepositoryInterface(),
            IOperation::FIELD__FILTER => FilterDefault::class,
            IOperation::FIELD__CLASS => Create::class,
            IOperation::FIELD__SPEC => [
                "request" => [
                    "type" => "object",
                    "properties" => [
                        "data" => [
                            "type" => "object",
                            "properties" => $attributes
                        ]
                    ]
                ],
                "response" => [
                    "type" => "object",
                    "properties" => $attributes
                ]
            ]
        ];
    }

    /**
     * @param IPluginInstallDefault $plugin
     * @param string $name
     * @param array $attributes
     *
     * @return array
     */
    protected function constructIndex(IPluginInstallDefault $plugin, string $name, array $attributes)
    {
        return [
            IOperation::FIELD__NAME => $name . '.index',
            IOperation::FIELD__TITLE => 'Index ' . $plugin->getPluginName(),
            IOperation::FIELD__DESCRIPTION => 'Index ' . $plugin->getPluginName(),
            IOperation::FIELD__METHOD => 'index',
            IOperation::FIELD__ITEM_NAME => $name,
            IOperation::FIELD__ITEM_CLASS => $plugin->getPluginItemClass(),
            IOperation::FIELD__ITEM_REPO => $plugin->getPluginRepositoryInterface(),
            IOperation::FIELD__FILTER => FilterDefault::class,
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
                            "properties" => $attributes
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
     * @param array $attributes
     *
     * @return array
     */
    protected function constructUpdate(IPluginInstallDefault $plugin, string $name, array $attributes)
    {
        return [
            IOperation::FIELD__NAME => $name . '.update',
            IOperation::FIELD__TITLE => 'Update ' . $plugin->getPluginName(),
            IOperation::FIELD__DESCRIPTION => 'Update ' . $plugin->getPluginName(),
            IOperation::FIELD__METHOD => 'update',
            IOperation::FIELD__ITEM_NAME => $name,
            IOperation::FIELD__ITEM_CLASS => $plugin->getPluginItemClass(),
            IOperation::FIELD__ITEM_REPO => $plugin->getPluginRepositoryInterface(),
            IOperation::FIELD__FILTER => FilterDefault::class,
            IOperation::FIELD__CLASS => Update::class,
            IOperation::FIELD__SPEC => [
                "request" => [
                    "type" => "object",
                    "properties" => [
                        "data" => [
                            "type" => "object",
                            "properties" => $attributes
                        ]
                    ]
                ],
                "response" => [
                    "type" => "object",
                    "properties" => $attributes
                ]
            ]
        ];
    }

    /**
     * @param IPluginInstallDefault $plugin
     * @param string $name
     * @param array $attributes
     *
     * @return array
     */
    protected function constructDelete(IPluginInstallDefault $plugin, string $name, array $attributes)
    {
        return [
            IOperation::FIELD__NAME => $name . '.delete',
            IOperation::FIELD__TITLE => 'Delete ' . $plugin->getPluginName(),
            IOperation::FIELD__DESCRIPTION => 'Delete ' . $plugin->getPluginName(),
            IOperation::FIELD__METHOD => 'delete',
            IOperation::FIELD__ITEM_NAME => $name,
            IOperation::FIELD__ITEM_CLASS => $plugin->getPluginItemClass(),
            IOperation::FIELD__ITEM_REPO => $plugin->getPluginRepositoryInterface(),
            IOperation::FIELD__FILTER => FilterDefault::class,
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
                    "properties" => $attributes
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
