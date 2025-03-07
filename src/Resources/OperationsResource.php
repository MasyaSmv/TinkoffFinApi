<?php

namespace TinkoffFinApi\Resources;

use Carbon\Carbon;
use Google\Protobuf\Timestamp;
use Tinkoff\Invest\V1\GetOperationsByCursorRequest;
use TinkoffFinApi\Exceptions\TinkoffApiException;
use TinkoffFinApi\Models\Operation;

class OperationsResource extends AbstractResource
{
    /**
     * Получить операцию по указанному ID
     *
     * @param $id
     *
     * @return Operation|null
     */
    public function findById($id): ?Operation
    {
        $accounts = $this->client->getAccounts()->all();
        foreach ($accounts as $account) {
            $operation = $account->getFindByIdOperation($id);
            
            if ($operation) {
                return $operation;
            }
        }

        return null;
    }

    /**
     * Получить все операции по указанному счету
     *
     * @param array $filters
     *
     * @return array
     */
    public function all(array $filters = []): array
    {
        if (!isset($filters['accountId'])) {
            throw new TinkoffApiException('Не передан accountId для получения операций.');
        }

        $accountId = $filters['accountId'];
        $from = $filters['from'] ?? Carbon::create(1970, 1, 1);
        $to = $filters['to'] ?? Carbon::now();

        return $this->getByDateRange((string)$accountId, $from, $to);
    }

    /**
     * Получить операции за период
     *
     * @param string $accountId
     * @param Carbon|null $from
     * @param Carbon|null $to
     *
     * @return Operation[]
     */
    public function getByDateRange(string $accountId, ?Carbon $from = null, ?Carbon $to = null): array
    {
        // Устанавливаем максимальное значение from по умолчанию (начало эпохи)
        $from = $from ?? Carbon::createFromTimestamp(0);
        // Если to не передано, ставим текущую дату
        $to = $to ?? Carbon::now();

        $operations = [];
        $cursor = ''; // Начинаем с пустого курсора

        do {
            $request = new GetOperationsByCursorRequest();

            // Устанавливаем from и to
            $fromTimestamp = new Timestamp();
            $fromTimestamp->setSeconds($from->getTimestamp());
            $request->setFrom($fromTimestamp);

            $toTimestamp = new Timestamp();
            $toTimestamp->setSeconds($to->getTimestamp());
            $request->setTo($toTimestamp);

            // Устанавливаем счёт
            $request->setAccountId($accountId);

            // Устанавливаем курсор (если он есть)
            $request->setCursor($cursor);

            // Устанавливаем лимит (по API Тинькофф можно до 100)
            $request->setLimit(100);

            // Вызываем API
            [$response, $status] = $this->callApi(
                fn() => $this->client->getClient()
                    ->operationsServiceClient
                    ->GetOperationsByCursor($request)
                    ->wait(),
            );

            // Если запрос неуспешен — бросаем исключение
            if ($status->code !== 0) {
                throw new TinkoffApiException("Error code: {$status->code}");
            }

            // Обрабатываем операции
            foreach ($response->getItems() as $op) {
                $operations[] = new Operation($op);
            }

            // Берем новый курсор и проверяем, есть ли ещё страницы
            $cursor = $response->getNextCursor();
            $hasNext = $response->getHasNext();
        } while ($hasNext);

        return $operations;
    }
}
