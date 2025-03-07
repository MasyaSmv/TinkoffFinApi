<?php

namespace TinkoffFinApi\Resources;

use TinkoffFinApi\Models\Operation;
use Tinkoff\Invest\V1\OperationsRequest;
use Google\Protobuf\Timestamp;
use Carbon\Carbon;

class OperationsResource extends AbstractResource
{
    /**
     * Получить операции за период
     *
     * @param string $accountId
     * @param Carbon $from
     * @param Carbon $to
     * @return Operation[]
     */
    public function getByDateRange(string $accountId, Carbon $from, Carbon $to): array
    {
        $request = new OperationsRequest();
        $fromTimestamp = new Timestamp();
        $fromTimestamp->setSeconds($from->getTimestamp());
        $request->setFrom($fromTimestamp);

        // ... заполнить $to

        [$response, $status] = $this->callApi(
            fn() => $this->client->getClient()
                ->operationsServiceClient
                ->GetOperations($request)
                ->wait()
        );

        // ... проверить $status->code

        $operations = [];
        foreach ($response->getOperations() as $op) {
            $operations[] = new Operation($op);
        }

        return $operations;
    }

    public function all(array $filters = [])
    {
        // TODO: Implement all() method.
    }

    public function findById($id)
    {
        // TODO: Implement findById() method.
    }
}
