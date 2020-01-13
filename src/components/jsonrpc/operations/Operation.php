<?php
namespace extas\components\jsonrpc\operations;

use extas\components\Item;
use extas\components\THasClass;
use extas\components\THasDescription;
use extas\components\THasName;
use extas\interfaces\jsonrpc\operations\IOperation;

/**
 * Class Operation
 *
 * @package extas\components\jsonrpc\routes
 * @author jeyroik@gmail.com
 */
class Operation extends Item implements IOperation
{
    use THasName;
    use THasDescription;
    use THasClass;

    /**
     * @return array
     */
    public function getSpec(): array
    {
        return $this->config[static::FIELD__SPEC] ?? [];
    }

    /**
     * @param array $spec
     *
     * @return IOperation
     */
    public function setSpec(array $spec): IOperation
    {
        $this->config[static::FIELD__SPEC] = $spec;

        return $this;
    }

    /**
     * @return string
     */
    protected function getSubjectForExtension(): string
    {
        return static::SUBJECT;
    }
}
