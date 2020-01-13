<?php
namespace extas\components\jsonrpc\operations\filters;

use extas\components\Item;
use extas\interfaces\jsonrpc\operations\IOperationFilter;

/**
 * Class FilterDefault
 *
 * @package extas\components\jsonrpc\operations\filters
 * @author jeyroik@gmail.com
 */
class FilterDefault extends Item implements  IOperationFilter
{
    /**
     * @param $currentValue
     * @param $valueToCompareWith
     * @param string $compare
     *
     * @return bool
     */
    public function isValid($currentValue, $valueToCompareWith, string $compare): bool
    {
        $isValid = false;

        if (method_exists($this, $compare)) {
            $isValid = $this->$compare($currentValue, $valueToCompareWith);
        } else {
            foreach ($this->getPluginsByStage('jsonrpc.filter.' . $compare) as $plugin) {
                $plugin($currentValue, $valueToCompareWith, $isValid);
            }
        }

        return $isValid;
    }

    /**
     * @param $currentValue
     * @param $valueToCompareWith
     *
     * @return bool
     */
    protected function eq($currentValue, $valueToCompareWith)
    {
        return $currentValue == $valueToCompareWith;
    }

    /**
     * @param $currentValue
     * @param $valueToCompareWith
     *
     * @return bool
     */
    protected function neq($currentValue, $valueToCompareWith)
    {
        return !$this->eq($currentValue, $valueToCompareWith);
    }

    /**
     * @param $currentValue
     * @param $valueToCompareWith
     *
     * @return bool
     */
    protected function gt($currentValue, $valueToCompareWith)
    {
        if (is_numeric($currentValue)) {
            return $currentValue > $valueToCompareWith;
        } elseif (is_string($currentValue)) {
            return strlen($currentValue) > strlen($valueToCompareWith);
        } else {
            return false;
        }
    }

    /**
     * @param $currentValue
     * @param $valueToCompareWith
     *
     * @return bool
     */
    protected function lt($currentValue, $valueToCompareWith)
    {
        if (is_numeric($currentValue)) {
            return $currentValue < $valueToCompareWith;
        } elseif (is_string($currentValue)) {
            return strlen($currentValue) < strlen($valueToCompareWith);
        } else {
            return false;
        }
    }

    /**
     * @param $currentValue
     * @param $valueToCompareWith
     *
     * @return bool
     */
    protected function gte($currentValue, $valueToCompareWith)
    {
        return !$this->lt($currentValue, $valueToCompareWith);
    }

    /**
     * @param $currentValue
     * @param $valueToCompareWith
     *
     * @return bool
     */
    protected function lte($currentValue, $valueToCompareWith)
    {
        return !$this->gt($currentValue, $valueToCompareWith);
    }

    /**
     * @param $currentValue
     * @param array|string $valueToCompareWith
     *
     * @return bool
     */
    protected function in($currentValue, $valueToCompareWith)
    {
        return in_array($currentValue, $valueToCompareWith);
    }

    /**
     * @param $currentValue
     * @param $valueToCompareWith
     *
     * @return bool
     */
    protected function nin($currentValue, $valueToCompareWith)
    {
        return !$this->in($currentValue, $valueToCompareWith);
    }

    /**
     * @param $currentValue
     * @param $valueToCompareWith
     *
     * @return bool
     */
    protected function like($currentValue, $valueToCompareWith)
    {
        return (strpos($currentValue, $valueToCompareWith) !== false);
    }

    /**
     * @param $currentValue
     * @param $valueToCompareWith
     *
     * @return bool
     */
    protected function nlike($currentValue, $valueToCompareWith)
    {
        return !$this->like($currentValue, $valueToCompareWith);
    }

    /**
     * @param $currentValue
     * @param $valueToCompareWith
     *
     * @return bool
     */
    protected function null($currentValue, $valueToCompareWith)
    {
        return empty($currentValue);
    }

    /**
     * @param $currentValue
     * @param $valueToCompareWith
     *
     * @return bool
     */
    protected function nnull($currentValue, $valueToCompareWith)
    {
        return !$this->null($currentValue, $valueToCompareWith);
    }

    /**
     * @return string
     */
    protected function getSubjectForExtension(): string
    {
        return static::SUBJECT;
    }
}
