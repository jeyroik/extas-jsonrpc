<?php
namespace extas\interfaces\jsonrpc;

use extas\interfaces\IHasId;
use extas\interfaces\IItem;
use Psr\Http\Message\RequestInterface;

/**
 * Interface IRequest
 *
 * @package extas\interfaces\jsonrpc
 * @author jeyroik@gmail.com
 */
interface IRequest extends IItem, IHasId
{
    const SUBJECT = 'extas.jsonrpc.request';

    const FIELD__METHOD = 'method';
    const FIELD__PARAMS = 'params';
    const FIELD__PARAMS_FILTER = 'filter';
    const FIELD__PARAMS_DATA = 'data';

    /**
     * @param RequestInterface $request
     *
     * @return IRequest
     */
    public static function fromHttp(RequestInterface $request);

    /**
     * @param string $default
     *
     * @return string
     */
    public function getMethod(string $default = ''): string;

    /**
     * @param array $default
     *
     * @return array
     */
    public function getParams(array $default  = []): array;

    /**
     * @param array $default
     *
     * @return array
     */
    public function getData(array $default = []): array;

    /**
     * @param array $default
     *
     * @return array
     */
    public function getFilter(array $default = []): array;

    /**
     * @param string $method
     *
     * @return IRequest
     */
    public function setMethod(string $method): IRequest;

    /**
     * @param array $params
     *
     * @return IRequest
     */
    public function setParams(array $params): IRequest;

    /**
     * @param array $filter
     *
     * @return IRequest
     */
    public function setFilter(array $filter): IRequest;
}
