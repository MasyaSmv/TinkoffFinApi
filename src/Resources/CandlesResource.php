<?php

namespace TinkoffFinApi\Resources;

use Carbon\Carbon;
use Google\Protobuf\Timestamp;
use Tinkoff\Invest\V1\CandleInterval;
use Tinkoff\Invest\V1\GetCandlesRequest;
use TinkoffFinApi\Exceptions\TinkoffApiException;
use TinkoffFinApi\Models\Candle;

class CandlesResource extends AbstractResource
{
    /**
     * Возвращает набор свечей по указанному инструменту.
     *
     * @param array $filters
     * @return Candle[]
     */
    public function all(array $filters = []): array
    {
        $request = new GetCandlesRequest();

        $this->fillInstrumentIdentifier($request, $filters);

        $from = $this->normalizeDate($filters['from'] ?? null, 'from');
        $to = $this->normalizeDate($filters['to'] ?? null, 'to');
        $interval = $this->normalizeInterval($filters['interval'] ?? null);

        $request->setFrom($this->createTimestamp($from));
        $request->setTo($this->createTimestamp($to));
        $request->setInterval($interval);

        [$response, $status] = $this->callApi(
            fn() => $this->client->getClient()
                ->marketDataServiceClient
                ->GetCandles($request)
                ->wait()
        );

        if ($status->code !== 0) {
            throw new TinkoffApiException("Error code: {$status->code}, msg: {$status->message}");
        }

        $candles = [];
        foreach ($response->getCandles() as $candle) {
            $candles[] = new Candle($candle, $this->client);
        }

        return $candles;
    }

    public function findById(string $id)
    {
        throw new TinkoffApiException('Поиск свечи по ID не поддерживается.');
    }

    private function fillInstrumentIdentifier(GetCandlesRequest $request, array $filters): void
    {
        $instrumentId = $filters['instrumentId'] ?? null;
        $figi = $filters['figi'] ?? null;
        $instrumentUid = $filters['instrumentUid'] ?? null;

        if ($instrumentId) {
            $request->setInstrumentId($instrumentId);
            return;
        }

        if ($figi) {
            $request->setFigi($figi);
            return;
        }

        if ($instrumentUid) {
            $request->setInstrumentUid($instrumentUid);
            return;
        }

        throw new TinkoffApiException('Не указан идентификатор инструмента (figi, instrumentId или instrumentUid).');
    }

    private function normalizeDate(mixed $date, string $name): Carbon
    {
        if ($date instanceof Carbon) {
            return $date;
        }

        if (is_string($date)) {
            try {
                return Carbon::parse($date);
            } catch (\Throwable $exception) {
                throw new TinkoffApiException("Некорректное значение даты {$name}.", 0, $exception);
            }
        }

        throw new TinkoffApiException("Для получения свечей необходимо передать дату {$name}.");
    }

    private function normalizeInterval(mixed $interval): int
    {
        if (is_int($interval)) {
            return $interval;
        }

        if (is_string($interval)) {
            $normalized = strtoupper(str_replace(['-', ' '], '_', $interval));
            $map = [
                '1MIN' => CandleInterval::CANDLE_INTERVAL_1_MIN,
                '5MIN' => CandleInterval::CANDLE_INTERVAL_5_MIN,
                '15MIN' => CandleInterval::CANDLE_INTERVAL_15_MIN,
                '1H' => CandleInterval::CANDLE_INTERVAL_HOUR,
                '1DAY' => CandleInterval::CANDLE_INTERVAL_DAY,
            ];

            if (isset($map[$normalized])) {
                return $map[$normalized];
            }
        }

        return CandleInterval::CANDLE_INTERVAL_1_MIN;
    }

    private function createTimestamp(Carbon $date): Timestamp
    {
        return tap(new Timestamp(), static fn(Timestamp $ts) => $ts->setSeconds($date->getTimestamp()));
    }
}
