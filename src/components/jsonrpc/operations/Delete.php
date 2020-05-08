<?php
namespace extas\components\jsonrpc\operations;

use extas\interfaces\IHasName;
use extas\interfaces\IItem;
use extas\interfaces\jsonrpc\operations\IOperationDelete;
use Psr\Http\Message\ResponseInterface;

/**
 * Class JsonRpcDelete
 *
 * @package extas\components\jsonrpc
 * @author jeyroik@gmail.com
 */
class Delete extends OperationDispatcher implements IOperationDelete
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
        $exist = $repo->all($request->getData());

        if (!$exist) {
            return $this->errorResponse(
                $request->getId(),
                'Unknown entity "' . ucfirst($this->getOperation()->getItemName()) . '"',
                404
            );
        }

        $repo->delete($request->getData());
        $result = [];
        foreach ($exist as $item) {
            $result[] = $item->__toArray();
        }
        return $this->successResponse($request->getId(), $result);
    }

    /**
     * @return string
     */
    protected function getSubjectForExtension(): string
    {
        return 'extas.jsonrpc.update';
    }
}
