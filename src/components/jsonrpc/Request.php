<?php
namespace extas\components\jsonrpc;

use extas\components\Item;
use extas\components\THasId;
use extas\interfaces\jsonrpc\IRequest;
use Psr\Http\Message\RequestInterface;

/**
 * Class Request
 *
 * @package extas\components\jsonrpc
 * @author jeyroik@gmail.com
 */
class Request extends Item implements IRequest
{
    use THasId;

    /**
     * @param RequestInterface $request
     *
     * @return IRequest
     */
    public static function fromHttp(RequestInterface $request)
    {
        return new static(json_decode($request->getBody()->getContents(), true));
    }

    /**
     * @param string $default
     *
     * @return string
     */
    public function getMethod(string $default = ''): string
    {
        return $this->config[static::FIELD__METHOD] ?? $default;
    }

    /**
     * @param array $default
     *
     * @return array
     */
    public function getParams(array $default = []): array
    {
        return $this->config[static::FIELD__PARAMS] ?? $default;
    }

    /**
     * @param array $default
     *
     * @return array
     */
    public function getData(array $default = []): array
    {
        $params = $this->getParams();

        return $params[static::FIELD__PARAMS_DATA] ?? $default;
    }

    /**
     * @param array $default
     *
     * @return array
     */
    public function getFilter(array $default = []): array
    {
        $params = $this->getParams();

        return $params[static::FIELD__PARAMS_FILTER] ?? $default;
    }

    /**
     * @param string $method
     *
     * @return IRequest
     */
    public function setMethod(string $method): IRequest
    {
        $this->config[static::FIELD__METHOD] = $method;

        return $this;
    }

    /**
     * @param array $params
     *
     * @return IRequest
     */
    public function setParams(array $params): IRequest
    {
        $this->config[static::FIELD__PARAMS] = $params;

        return $this;
    }

    /**
     * @param array $filter
     *
     * @return IRequest
     */
    public function setFilter(array $filter): IRequest
    {
        $params = $this->getParams();
        $params[static::FIELD__PARAMS_FILTER] = $filter;

        return $this->setParams($params);
    }

    /**
     * @return string
     */
    protected function getSubjectForExtension(): string
    {
        return static::SUBJECT;
    }
}
