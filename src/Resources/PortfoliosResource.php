<?php

namespace TinkoffFinApi\Resources;

use App\Helpers\BrokerParsers\Tinkoff\TinkoffPortfolio;
use Tinkoff\Invest\V1\PortfolioRequest;
use Tinkoff\Invest\V1\PortfolioRequest\CurrencyRequest;
use Tinkoff\Invest\V1\PortfolioResponse;
use TinkoffFinApi\Exceptions\TinkoffApiException;
use TinkoffFinApi\Models\Portfolio;

class PortfoliosResource extends AbstractResource
{
    /**
     * Получить список счетов (портфель) для указанного accountId.
     *
     * @param array $filters Обязательно должен содержать 'accountId'.
     * Опционально можно передать 'currencyId' (по умолчанию 0).
     *
     * @return Portfolio[]
     *
     * @throws TinkoffApiException Если не передан accountId или произошла ошибка API.
     */
    public function all(array $filters = []): array
    {
        $portfolios = [];
        $accounts = $this->client->getAccounts()->all();

        foreach ($accounts as $account) {
            //Задаем параметры для подключения к портфелю счета
            $request = new PortfolioRequest();
            $request->setAccountId($account->id);
            $request->setCurrency($filters['currencyId'] ?? CurrencyRequest::RUB);

            /** @var PortfolioResponse $response Выполняем API вызов к сервису портфеля */
            [$response, $status] = $this->callApi(fn() => $this->client->getClient()
                ->operationsServiceClient
                ->GetPortfolio($request)
                ->wait(),
            );

            if ($status->code !== 0) {
                throw new TinkoffApiException("Error code: {$status->code}, msg: {$status->message}");
            }

            $portfolios[$account->id] = new Portfolio($response, $this->client);
        }

        return $portfolios;
    }

    /**
     * Получить счет по-указанному ID.
     *
     * Для получения одного счета используем отдельный запрос, который возвращает список всех счетов,
     * затем фильтруем нужный по ID.
     *
     * @param mixed $id Идентификатор счета
     *
     * @return Portfolio|null Найденный счет или null, если счет не найден
     *
     * @throws TinkoffApiException При ошибке API
     */
    public function findById($id): ?Portfolio
    {
        $request = new PortfolioRequest();
        $request->setAccountId($id);

        /** @var PortfolioResponse $response Выполняем API вызов к сервису портфеля */
        [$response, $status] = $this->callApi(fn() => $this->client->getClient()
            ->operationsServiceClient
            ->GetPortfolio($request)
            ->wait(),
        );

        if ($status->code !== 0) {
            throw new TinkoffApiException("Error code: {$status->code}, msg: {$status->message}");
        }

        // Дополнительная проверка: если ответ пустой или не содержит ожидаемых данных, можно вернуть null
        if (!$response) {
            return null;
        }

        return new Portfolio($response, $this->client);
    }
}
