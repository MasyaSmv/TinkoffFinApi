<?php

namespace TinkoffFinApi\Models;

use Carbon\Carbon;
use Google\Protobuf\Timestamp;
use Tinkoff\Invest\V1\MoneyValue;
use Tinkoff\Invest\V1\Quotation;
use TinkoffFinApi\Client\TinkoffFinApiClient;

abstract class AbstractModel
{
    protected TinkoffFinApiClient $client;

    public function __construct(TinkoffFinApiClient $client)
    {
        $this->client = $client;
    }

    /**
     *  Преобразует поле из GrpcAccount в Carbon или null,
     *  если поле отсутствует либо содержит 0.
     *
     * @param Timestamp|null $timestamp
     *
     * @return Carbon|null
     */
    protected function makeCarbon(?Timestamp $timestamp): ?Carbon
    {
        if (!$timestamp) {
            return null;
        }

        $seconds = $timestamp->getSeconds();
        $nanos = $timestamp->getNanos();

        // Проверяем «нулевую» дату
        if ($seconds === 0 && $nanos === 0) {
            return null;
        }

        return Carbon::createFromTimestamp($seconds);
    }

    /**
     * Преобразует объект MoneyValue в float значение.
     *
     * Объект MoneyValue содержит две части:
     * - units: целая часть суммы;
     * - nano: дробная часть в наносекундах (1e-9 от единицы).
     *
     * Например, если units = 239, а nano = 400000000, итоговое значение:
     * 239 + 400000000 / 1_000_000_000 = 239.4
     *
     * @param MoneyValue|Quotation $moneyValue Объект с денежным значением
     *
     * @return string Итоговое значение суммы в виде float + код валюты
     */
    protected function convertToFloat(MoneyValue|Quotation $moneyValue): string
    {
        // Получаем целую часть из объекта
        $units = $moneyValue->getUnits();
        // Получаем дробную часть в наносекундах
        $nano = $moneyValue->getNano();
        // Преобразуем nano в дробное значение (nano представляет часть в миллиардной)
        // и складываем с целой частью, получая итоговое число
        $float = $units + ($nano / 10 ** 9);

        // Если объект имеет метод getCurrency (например, MoneyValue), добавляем валюту к результату
        if (method_exists($moneyValue, 'getCurrency') && !empty($moneyValue->getCurrency())) {
            $currency = $moneyValue->getCurrency();
            // Форматируем число с валютой и возвращаем результат
            return trim($float) . ' ' . trim($currency);
        }

        // Если валюты нет (например, в объекте Quotation), возвращаем только число
        return (string)$float;
    }
}