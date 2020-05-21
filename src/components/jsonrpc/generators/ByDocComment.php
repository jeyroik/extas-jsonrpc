<?php
namespace extas\components\jsonrpc\generators;

use extas\components\jsonrpc\crawlers\ByDocComment as Crawler;
use extas\interfaces\jsonrpc\operations\IOperation;

/**
 * Class ByDocComment
 *
 * @package extas\components\jsonrpc\generators
 * @author jeyroik <jeyroik@gmail.com>
 */
class ByDocComment extends GeneratorDispatcher
{
    protected string $docComment = '';

    /**
     * @param array $applicableClasses
     * @throws \ReflectionException
     */
    public function __invoke(array $applicableClasses): void
    {
        if (isset($applicableClasses[Crawler::NAME])) {
            $this->generate($applicableClasses);
        }
    }

    /**
     * @param array $operations
     * @return bool
     * @throws \ReflectionException
     */
    public function generate(array $operations): bool
    {
        foreach ($operations as $operation) {
            $reflection = new \ReflectionClass($operation);
            $this->docComment = $reflection->getDocComment();
            if ($this->isApplicableOperation($this->getOperationName())) {
                $this->addOperation($this->buildOperation($operation));
            }
        }

        $this->exportGeneratedData();

        return true;
    }

    /**
     * @param $operation
     * @return array
     */
    protected function buildOperation($operation): array
    {
        return [
            IOperation::FIELD__NAME => $this->getOperationName(),
            IOperation::FIELD__TITLE => $this->getOperationTitle(),
            IOperation::FIELD__DESCRIPTION => $this->getOperationDescription(),
            IOperation::FIELD__METHOD => '',
            IOperation::FIELD__ITEM_NAME => '',
            IOperation::FIELD__ITEM_CLASS => '',
            IOperation::FIELD__ITEM_REPO => '',
            IOperation::FIELD__CLASS => get_class($operation),
            IOperation::FIELD__SPEC => [
                "request" => ["type" => "object", "properties" => $this->getRequestProperties()],
                "response" => ["type" => "object", "properties" => $this->getResponseProperties()]
            ]
        ];
    }

    /**
     * @return string
     */
    protected function getOperationName(): string
    {
        return $this->oneByPattern('name');
    }

    /**
     * @return string
     */
    protected function getOperationTitle(): string
    {
        return $this->oneByPattern('title');
    }

    /**
     * @return string
     */
    protected function getOperationDescription(): string
    {
        return $this->oneByPattern('description');
    }

    /**
     * @return array
     */
    protected function getRequestProperties(): array
    {
        return $this->getProperties('request');
    }

    /**
     * @return array
     */
    protected function getResponseProperties(): array
    {
        return $this->getProperties('response');
    }

    /**
     * @param string $prefix
     * @return array
     */
    protected function getProperties(string $prefix): array
    {
        $fields = $this->allByPattern($prefix . '_field');
        $properties = [];
        foreach ($fields as $field) {
            list($propertyName, $propertyType) = explode(':', $field);
            $properties[$propertyName] = ['type' => $propertyType];
        }

        return $properties;
    }

    /**
     * @param string $subject
     * @return string
     */
    protected function oneByPattern(string $subject): string
    {
        preg_match_all('/@jsonrpc_' . $subject . '\s(.*)/', $this->docComment, $matches);

        $result = '';
        if (!empty($matches[0])) {
            $result = array_shift($matches[1]);
        }

        return $result;
    }

    /**
     * @param string $subject
     * @return array
     */
    protected function allByPattern(string $subject): array
    {
        preg_match_all('/@jsonrpc_' . $subject . '\s(.*)/', $this->docComment, $matches);

        $result = [];
        if (!empty($matches[0])) {
            $result = $matches[1];
        }

        return $result;
    }
}
