<?php
namespace extas\components\jsonrpc\operations;

use extas\interfaces\IHasName;
use extas\interfaces\IItem;
use extas\interfaces\jsonrpc\IRequest;
use extas\interfaces\jsonrpc\operations\IOperationUpdate;
use Psr\Http\Message\ResponseInterface;

/**
 * Class JsonRpcUpdate
 *
 * @package extas\components\jsonrpc
 * @author jeyroik@gmail.com
 */
class Update extends OperationDispatcher implements IOperationUpdate
{
    /**
     * @return ResponseInterface
     */
    public function __invoke(): ResponseInterface
    {
        $repo = $this->getItemRepo();
        $request = $this->convertPsrToJsonRpcRequest();
        $item = $this->getItem($request);
        $pkMethod = 'get' . ucfirst($repo->getPk());

        if (!method_exists($item, $pkMethod)) {
            return $this->errorResponse($request->getId(), $this->error(500, $pkMethod), 500);
        }

        $exist = $repo->one([IHasName::FIELD__NAME => $item->$pkMethod()]);

        if (!$exist) {
            return $this->errorResponse($request->getId(), $this->error(404), 404);
        }

        $this->updateItemFields($item, $exist);
        $repo->update($item);

        return $this->successResponse($request->getId(), [$item->__toArray()]);
    }

    /**
     * @param int $code
     * @param string $subject
     * @return string
     */
    protected function error(int $code, string $subject = ''): string
    {
        $map = [
            404 => function () {
                return 'Unknown entity "' . ucfirst($this->getOperation()->getItemName()) . '"';
            },
            500 => function () use ($subject) {
                return 'Item has not method "' . $subject . '"';
            }
        ];

        return $map[$code] ? $map[$code]($subject) : '';
    }

    /**
     * @param IRequest $request
     * @return IItem
     */
    protected function getItem(IRequest $request): IItem
    {
        $itemClass = $this->getOperation()->getItemClass();
        return new $itemClass($request->getData());
    }

    /**
     * @param IItem $item
     * @param IItem $updateWith
     */
    protected function updateItemFields(IItem &$item, IItem $updateWith): void
    {
        foreach ($updateWith as $field => $value) {
            if (!isset($item[$field])) {
                $item[$field] = $value;
            }
        }
    }

    /**
     * @return string
     */
    protected function getSubjectForExtension(): string
    {
        return 'extas.jsonrpc.update';
    }
}
