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
        // Если from или to не заданы, устанавливаем значения по умолчанию
        $from ??= Carbon::createFromTimestamp(0);
        $to ??= Carbon::now();

        $operations = [];
        $cursor = '';

        do {
            $request = new GetOperationsByCursorRequest();

            // Создаем объекты Timestamp и сразу инициализируем их
            $request->setFrom(tap(new Timestamp(), static fn(Timestamp $ts) => $ts->setSeconds($from->getTimestamp())));
            $request->setTo(tap(new Timestamp(), static fn(Timestamp $ts) => $ts->setSeconds($to->getTimestamp())));

            $request->setAccountId($accountId);
            $request->setCursor($cursor);
            $request->setLimit(100);

            // Вызываем API и получаем ответ
            [$response, $status] = $this->callApi(fn() => $this->client->getClient()
                ->operationsServiceClient
                ->GetOperationsByCursor($request)
                ->wait(),
            );

            // Используем конструкцию match для проверки статуса и выбрасывания исключения в одну строчку
            match ($status->code) {
                0 => null,
                default => throw new TinkoffApiException("Error code: {$status->code}"),
            };

            // Обрабатываем полученные операции
            foreach ($response->getItems() as $op) {
                $operations[] = new Operation($op, $this->client);
            }

            // Получаем курсор для следующей страницы
            $cursor = $response->getNextCursor();
        } while ($response->getHasNext());

        return $operations;
    }
}
