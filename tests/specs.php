<?php

use extas\interfaces\jsonrpc\operations\IOperation;
use extas\components\jsonrpc\operations\Operation;
use extas\components\jsonrpc\operations\Create;
use extas\components\jsonrpc\operations\Index;
use extas\components\jsonrpc\operations\Update;
use extas\components\jsonrpc\operations\Delete;

return [
    [
        IOperation::FIELD__NAME => 'jsonrpc.operation.create',
        IOperation::FIELD__TITLE => 'Create jsonrpc operation',
        IOperation::FIELD__DESCRIPTION => 'Create jsonrpc operation',
        IOperation::FIELD__METHOD => 'create',
        IOperation::FIELD__ITEM_NAME => 'jsonrpc.operation',
        IOperation::FIELD__ITEM_CLASS => Operation::class,
        IOperation::FIELD__ITEM_REPO => 'jsonRpcOperationRepository',
        IOperation::FIELD__CLASS => Create::class,
        IOperation::FIELD__SPEC => [
            "request" => [
                "type" => "object",
                "properties" => [
                    "data" => [
                        "type" => "object",
                        "properties" => [
                            "spec" => ["type" => "array"],
                            "item_name" => ["type" => "string"],
                            "item_class" => ["type" => "string"],
                            "item_repo" => ["type" => "string"],
                            "method" => ["type" => "string"],
                            "name" => ["type" => "string"],
                            "title" => ["type" => "string"],
                            "description" => ["type" => "string"],
                            "class" => ["type" => "string"]
                        ]
                    ]
                ]
            ],
            "response" => [
                "type" => "object",
                "properties" => [
                    "spec" => ["type" => "array"],
                    "item_name" => ["type" => "string"],
                    "item_class" => ["type" => "string"],
                    "item_repo" => ["type" => "string"],
                    "method" => ["type" => "string"],
                    "name" => ["type" => "string"],
                    "title" => ["type" => "string"],
                    "description" => ["type" => "string"],
                    "class" => ["type" => "string"]
                ]
            ]
        ]
    ],
    [
        IOperation::FIELD__NAME => 'jsonrpc.operation.index',
        IOperation::FIELD__TITLE => 'Index jsonrpc operation',
        IOperation::FIELD__DESCRIPTION => 'Index jsonrpc operation',
        IOperation::FIELD__METHOD => 'index',
        IOperation::FIELD__ITEM_NAME => 'jsonrpc.operation',
        IOperation::FIELD__ITEM_CLASS => Operation::class,
        IOperation::FIELD__ITEM_REPO => 'jsonRpcOperationRepository',
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
                        "properties" => [
                            "spec" => ["type" => "array"],
                            "item_name" => ["type" => "string"],
                            "item_class" => ["type" => "string"],
                            "item_repo" => ["type" => "string"],
                            "method" => ["type" => "string"],
                            "name" => ["type" => "string"],
                            "title" => ["type" => "string"],
                            "description" => ["type" => "string"],
                            "class" => ["type" => "string"]
                        ]
                    ],
                    "total" => [
                        "type" => "number"
                    ]
                ]
            ]
        ]
    ],[
        IOperation::FIELD__NAME => 'jsonrpc.operation.update',
        IOperation::FIELD__TITLE => 'Update jsonrpc operation',
        IOperation::FIELD__DESCRIPTION => 'Update jsonrpc operation',
        IOperation::FIELD__METHOD => 'update',
        IOperation::FIELD__ITEM_NAME => 'jsonrpc.operation',
        IOperation::FIELD__ITEM_CLASS => Operation::class,
        IOperation::FIELD__ITEM_REPO => 'jsonRpcOperationRepository',
        IOperation::FIELD__CLASS => Update::class,
        IOperation::FIELD__SPEC => [
            "request" => [
                "type" => "object",
                "properties" => [
                    "data" => [
                        "type" => "object",
                        "properties" => [
                            "spec" => ["type" => "array"],
                            "item_name" => ["type" => "string"],
                            "item_class" => ["type" => "string"],
                            "item_repo" => ["type" => "string"],
                            "method" => ["type" => "string"],
                            "name" => ["type" => "string"],
                            "title" => ["type" => "string"],
                            "description" => ["type" => "string"],
                            "class" => ["type" => "string"]
                        ]
                    ]
                ]
            ],
            "response" => [
                "type" => "object",
                "properties" => [
                    "spec" => ["type" => "array"],
                    "item_name" => ["type" => "string"],
                    "item_class" => ["type" => "string"],
                    "item_repo" => ["type" => "string"],
                    "method" => ["type" => "string"],
                    "name" => ["type" => "string"],
                    "title" => ["type" => "string"],
                    "description" => ["type" => "string"],
                    "class" => ["type" => "string"]
                ]
            ]
        ]
    ],[
        IOperation::FIELD__NAME => 'jsonrpc.operation.delete',
        IOperation::FIELD__TITLE => 'Delete jsonrpc operation',
        IOperation::FIELD__DESCRIPTION => 'Delete jsonrpc operation',
        IOperation::FIELD__METHOD => 'delete',
        IOperation::FIELD__ITEM_NAME => 'jsonrpc.operation',
        IOperation::FIELD__ITEM_CLASS => Operation::class,
        IOperation::FIELD__ITEM_REPO => 'jsonRpcOperationRepository',
        IOperation::FIELD__CLASS => Delete::class,
        IOperation::FIELD__SPEC => [
            "request" => [
                "type" => "object",
                "properties" => [
                    "data" => [
                        "type" => "object",
                        "properties" => [
                            "spec" => ["type" => "array"],
                            "item_name" => ["type" => "string"],
                            "item_class" => ["type" => "string"],
                            "item_repo" => ["type" => "string"],
                            "method" => ["type" => "string"],
                            "name" => ["type" => "string"],
                            "title" => ["type" => "string"],
                            "description" => ["type" => "string"],
                            "class" => ["type" => "string"]
                        ]
                    ]
                ]
            ],
            "response" => [
                "type" => "object",
                "properties" => [
                    "spec" => ["type" => "array"],
                    "item_name" => ["type" => "string"],
                    "item_class" => ["type" => "string"],
                    "item_repo" => ["type" => "string"],
                    "method" => ["type" => "string"],
                    "name" => ["type" => "string"],
                    "title" => ["type" => "string"],
                    "description" => ["type" => "string"],
                    "class" => ["type" => "string"]
                ]
            ]
        ]
    ],
];
