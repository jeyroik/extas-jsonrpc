<?php
namespace extas\components\jsonrpc\operations;

use extas\interfaces\IHasName;
use extas\interfaces\IItem;
use extas\interfaces\jsonrpc\IRequest;
use extas\interfaces\jsonrpc\operations\IOperationCreate;
use extas\interfaces\repositories\IRepository;
use Psr\Http\Message\ResponseInterface;

/**
 * Class Create
 *
 * @package extas\components\jsonrpc
 * @author jeyroik@gmail.com
 */
class Create extends OperationDispatcher implements IOperationCreate
{
    /**
     * @return ResponseInterface
     */
    public function __invoke(): ResponseInterface
    {
        /**
         * @var $item IItem|IHasName
         */
        $repo = $this->getItemRepo();
        $request = $this->convertPsrToJsonRpcRequest();
        $item = $this->getItem($request);
        $pkMethod = $this->getPkMethod($repo);

        if (!method_exists($item, $pkMethod)) {
            return $this->errorResponse($request->getId(), $this->error(500, $pkMethod), 500);
        }

        $exist = $repo->one([$repo->getPk() => $item->$pkMethod()]);

        if ($exist || !$item->$pkMethod()) {
            return $this->errorResponse($request->getId(), $this->error(400), 400);
        }

        $item = $repo->create($item);
        return $this->successResponse($request->getId(), $item->__toArray());
    }

    /**
     * @param int $code
     * @param string $subject
     * @return string
     */
    protected function error(int $code, string $subject = ''): string
    {
        $map = [
            400 => function () {
                return ucfirst($this->getOperation()->getItemName()) . ' already exist';
            },
            500 => function () use ($subject) {
                return 'Item has not method "' . $subject . '"';
            }
        ];

        return $map[$code] ? $map[$code]($subject) : '';
    }

    /**
     * @param IRepository $repo
     * @return string
     */
    protected function getPkMethod(IRepository $repo): string
    {
        return 'get' . ucfirst($repo->getPk());
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
     * @return string
     */
    protected function getSubjectForExtension(): string
    {
        return 'extas.jsonrpc.create';
    }
}
