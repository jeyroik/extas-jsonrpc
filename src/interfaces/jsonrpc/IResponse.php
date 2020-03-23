<?php
namespace extas\interfaces\jsonrpc;

use extas\interfaces\IItem;
use Psr\Http\Message\ResponseInterface;

/**
 * Interface IResponse
 *
 * @package extas\interfaces\jsonrpc
 * @author jeyroik@gmail.com
 */
interface IResponse extends IItem
{
    const SUBJECT = 'extas.jsonrpc.response';

    const FIELD__RESPONSE = 'response';
    const FIELD__DATA = 'data';

    const RESPONSE__ID = 'id';
    const RESPONSE__VERSION = 'jsonrpc';
    const RESPONSE__RESULT = 'result';
    const RESPONSE__ERROR = 'error';
    const RESPONSE__ERROR_CODE = 'code';
    const RESPONSE__ERROR_DATA = 'data';
    const RESPONSE__ERROR_MESSAGE = 'message';
    const RESPONSE__ERROR_MARKER = '@error';

    const VERSION_CURRENT = '2.0';

    /**
     * @param ResponseInterface $response
     *
     * @return IResponse
     */
    public static function fromPsr(ResponseInterface $response): IResponse;

    /**
     * @return bool
     */
    public function hasError(): bool;

    /**
     * @return null|ResponseInterface
     */
    public function getPsrResponse(): ?ResponseInterface;

    /**
     * @param ResponseInterface $response
     *
     * @return IResponse
     */
    public function setPsrResponse(ResponseInterface $response): IResponse;

    /**
     * @return array
     */
    public function getData(): array;

    /**
     * @param array $data
     *
     * @return IResponse
     */
    public function setData(array $data): IResponse;

    /**
     * @param string $message
     * @param int $code
     * @param array $data
     *
     * @return IResponse
     */
    public function error(string $message, int $code, array $data = []): IResponse;

    /**
     * @param mixed $data
     *
     * @return IResponse
     */
    public function success($data): IResponse;
}
