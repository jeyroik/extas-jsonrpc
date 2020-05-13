<?php

use extas\interfaces\jsonrpc\operations\IOperation;
use tests\ItemWithDocComment;
use extas\interfaces\jsonrpc\operations\IOperationRepository;
use extas\components\jsonrpc\operations\Create;
use extas\components\jsonrpc\operations\Index;
use extas\components\jsonrpc\operations\Update;
use extas\components\jsonrpc\operations\Delete;

return [
    [
        IOperation::FIELD__NAME => 'item.doc.create',
        IOperation::FIELD__TITLE => 'Create item doc',
        IOperation::FIELD__DESCRIPTION => 'Create item doc',
        IOperation::FIELD__METHOD => 'create',
        IOperation::FIELD__ITEM_NAME => 'item.doc',
        IOperation::FIELD__ITEM_CLASS => ItemWithDocComment::class,
        IOperation::FIELD__ITEM_REPO => 'Some\\Repo',
        IOperation::FIELD__CLASS => Create::class,
        IOperation::FIELD__SPEC => [
            "request" => [
                "type" => "object",
                "properties" => [
                    "data" => [
                        "type" => "object",
                        "properties" => [
                            "name" => ["type" => "string"],
                            "title" => ["type" => "string"]
                        ]
                    ]
                ]
            ],
            "response" => [
                "type" => "object",
                "properties" => [
                    "name" => ["type" => "string"],
                    "title" => ["type" => "string"]
                ]
            ]
        ]
    ],
    [
        IOperation::FIELD__NAME => 'item.doc.index',
        IOperation::FIELD__TITLE => 'Index item doc',
        IOperation::FIELD__DESCRIPTION => 'Index item doc',
        IOperation::FIELD__METHOD => 'index',
        IOperation::FIELD__ITEM_NAME => 'item.doc',
        IOperation::FIELD__ITEM_CLASS => ItemWithDocComment::class,
        IOperation::FIELD__ITEM_REPO => 'Some\\Repo',
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
                            "name" => ["type" => "string"],
                            "title" => ["type" => "string"]
                        ]
                    ],
                    "total" => [
                        "type" => "number"
                    ]
                ]
            ]
        ]
    ],[
        IOperation::FIELD__NAME => 'item.doc.update',
        IOperation::FIELD__TITLE => 'Update item doc',
        IOperation::FIELD__DESCRIPTION => 'Update item doc',
        IOperation::FIELD__METHOD => 'update',
        IOperation::FIELD__ITEM_NAME => 'item.doc',
        IOperation::FIELD__ITEM_CLASS => ItemWithDocComment::class,
        IOperation::FIELD__ITEM_REPO => 'Some\\Repo',
        IOperation::FIELD__CLASS => Update::class,
        IOperation::FIELD__SPEC => [
            "request" => [
                "type" => "object",
                "properties" => [
                    "data" => [
                        "type" => "object",
                        "properties" => [
                            "name" => ["type" => "string"],
                            "title" => ["type" => "string"]
                        ]
                    ]
                ]
            ],
            "response" => [
                "type" => "object",
                "properties" => [
                    "name" => ["type" => "string"],
                    "title" => ["type" => "string"]
                ]
            ]
        ]
    ],[
        IOperation::FIELD__NAME => 'item.doc.delete',
        IOperation::FIELD__TITLE => 'Delete item doc',
        IOperation::FIELD__DESCRIPTION => 'Delete item doc',
        IOperation::FIELD__METHOD => 'delete',
        IOperation::FIELD__ITEM_NAME => 'item.doc',
        IOperation::FIELD__ITEM_CLASS => ItemWithDocComment::class,
        IOperation::FIELD__ITEM_REPO => 'Some\\Repo',
        IOperation::FIELD__CLASS => Delete::class,
        IOperation::FIELD__SPEC => [
            "request" => [
                "type" => "object",
                "properties" => [
                    "data" => [
                        "type" => "object",
                        "properties" => [
                            "name" => ["type" => "string"],
                            "title" => ["type" => "string"]
                        ]
                    ]
                ]
            ],
            "response" => [
                "type" => "object",
                "properties" => [
                    "name" => ["type" => "string"],
                    "title" => ["type" => "string"]
                ]
            ]
        ]
    ],
];