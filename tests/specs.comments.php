<?php

use extas\interfaces\jsonrpc\operations\IOperation;

return [
    [
        IOperation::FIELD__NAME => 'test',
        IOperation::FIELD__TITLE => 'Test',
        IOperation::FIELD__DESCRIPTION => 'This is operation for tests only',
        IOperation::FIELD__METHOD => '',
        IOperation::FIELD__ITEM_NAME => '',
        IOperation::FIELD__ITEM_CLASS => '',
        IOperation::FIELD__ITEM_REPO => '',
        IOperation::FIELD__CLASS => \tests\OperationWithDocComment::class,
        IOperation::FIELD__SPEC => [
            "request" => [
                "type" => "object",
                "properties" => [
                    "id" => ["type" => "string"],
                    "name" => ["type" => "string"]
                ]
            ],
            "response" => [
                "type" => "object",
                "properties" => [
                    "id" => ["type" => "int"],
                    "parameters" => ["type" => "array"]
                ]
            ]
        ]
    ]
];
