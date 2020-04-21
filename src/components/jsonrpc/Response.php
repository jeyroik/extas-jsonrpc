<?php
namespace extas\components\jsonrpc;

use extas\components\Item;
use extas\interfaces\jsonrpc\IRequest;
use extas\interfaces\jsonrpc\IResponse;
use Psr\Http\Message\ResponseInterface;

/**
 * Class Response
 *
 * @package extas\components\jsonrpc
 * @author jeyroik@gmail.com
 */
class Response extends Item implements IResponse
{
    protected bool $hasError = false;

    /**
     * @param ResponseInterface $response
     *
     * @return IResponse
     */
    public static function fromPsr(ResponseInterface $response): IResponse
    {
        return new static([static::FIELD__RESPONSE => $response]);
    }

    /**
     * Response constructor.
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        parent::__construct($config);

        $this->setPsrResponse(
            $this->getPsrResponse()
                ->withHeader('Content-type', 'application/json')
                ->withStatus(200)
        );
    }

    /**
     * @return bool
     */
    public function hasError(): bool
    {
        return $this->hasError;
    }

    /**
     * @return null|ResponseInterface
     */
    public function getPsrResponse(): ?ResponseInterface
    {
        return $this->config[static::FIELD__RESPONSE] ?? null;
    }

    /**
     * @param ResponseInterface $response
     *
     * @return IResponse
     */
    public function setPsrResponse(ResponseInterface $response): IResponse
    {
        $this->config[static::FIELD__RESPONSE] = $response;

        return $this;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->config[static::FIELD__DATA] ?? [];
    }

    /**
     * @param array $data
     *
     * @return IResponse
     */
    public function setData(array $data): IResponse
    {
        $this->config[static::FIELD__DATA] = $data;

        return $this;
    }

    /**
     * @param $data
     *
     * @return IResponse
     */
    public function success($data): IResponse
    {
        $response = $this->getPsrResponse();
        $response->getBody()->write(json_encode(
            [
                static::RESPONSE__ID => $this->getData()[IRequest::FIELD__ID] ?? '',
                static::RESPONSE__VERSION => static::VERSION_CURRENT,
                static::RESPONSE__RESULT => $data
            ]
        ));

        $this->setData($data);
        $this->setPsrResponse($response);

        return $this;
    }

    /**
     * @param string $message
     * @param int $code
     * @param array $data
     *
     * @return IResponse
     */
    public function error(string $message, int $code, $data = []): IResponse
    {
        $response = $this->getPsrResponse();
        $response->getBody()->write(json_encode(
            [
                static::RESPONSE__ID => $this->getData()[IRequest::FIELD__ID] ?? '',
                static::RESPONSE__VERSION => '2.0',
                static::RESPONSE__ERROR => [
                    static::RESPONSE__ERROR_CODE => $code,
                    static::RESPONSE__ERROR_DATA => $data,
                    static::RESPONSE__ERROR_MESSAGE => $message
                ]
            ]
        ));

        $this->setData($data);
        $this->setPsrResponse($response);

        $this->hasError = true;

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
