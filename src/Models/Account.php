<?php

namespace TinkoffFinApi\Models;

use Carbon\Carbon;
use Google\Protobuf\Timestamp;
use Tinkoff\Invest\V1\Account as GrpcAccount;

class Account extends AbstractModel
{
    /**
     * Идентификатор счёта.
     *
     * @var string
     */
    public string $id = '';

    /**
     * AccountType    Тип счёта.
     * ACCOUNT_TYPE_UNSPECIFIED    0    Тип аккаунта не определён.
     * ACCOUNT_TYPE_TINKOFF    1    Брокерский счёт Тинькофф.
     * ACCOUNT_TYPE_TINKOFF_IIS    2    ИИС счёт.
     * ACCOUNT_TYPE_INVEST_BOX    3    Инвесткопилка.
     *
     * @var int
     */
    public int $type = 0;

    /**
     * Название счёта.
     *
     * @var string
     */
    public string $name = '';

    /**
     * AccountStatus    Статус счёта.
     * ACCOUNT_STATUS_UNSPECIFIED    0    Статус счёта не определён.
     * ACCOUNT_STATUS_NEW    1    Новый, в процессе открытия.
     * ACCOUNT_STATUS_OPEN    2    Открытый и активный счёт.
     * ACCOUNT_STATUS_CLOSED    3    Закрытый счёт.
     *
     * @var int
     */
    public int $status = 0;

    /**
     * Дата открытия счёта
     *
     * @var Carbon|null
     */
    public ?Carbon $opened_date = null;

    /**
     * Дата закрытия счёта
     *
     * @var Carbon|null
     */
    public ?Carbon $closed_date = null;

    /**
     * AccessLevel    Уровень доступа к текущему счёту (определяется токеном).
     * ACCOUNT_ACCESS_LEVEL_UNSPECIFIED    0    Уровень доступа не определён.
     * ACCOUNT_ACCESS_LEVEL_FULL_ACCESS    1    Полный доступ к счёту.
     * ACCOUNT_ACCESS_LEVEL_READ_ONLY    2    Доступ с уровнем прав "только чтение".
     * ACCOUNT_ACCESS_LEVEL_NO_ACCESS    3    Доступ отсутствует.
     *
     * @var int
     */
    public int $access_level = 0;

    /**
     * @param GrpcAccount $grpcAccount
     */
    public function __construct(GrpcAccount $grpcAccount)
    {
        $this->id = $grpcAccount->getId();
        $this->status = $grpcAccount->getStatus();
        $this->name = $grpcAccount->getName();
        $this->opened_date = $this->makeCarbon($grpcAccount->getOpenedDate());
        $this->closed_date = $this->makeCarbon($grpcAccount->getClosedDate());
        $this->type = $grpcAccount->getType();
        $this->access_level = $grpcAccount->getAccessLevel();
    }

    /**
     *  Преобразует поле из GrpcAccount в Carbon или null,
     *  если поле отсутствует либо содержит 0.
     *
     * @param Timestamp|null $timestamp
     *
     * @return Carbon|null
     */
    private function makeCarbon(?Timestamp $timestamp): ?Carbon
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
}
