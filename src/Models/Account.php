<?php

namespace TinkoffFinApi\Models;

use Carbon\Carbon;
use Google\Protobuf\Timestamp;
use Tinkoff\Invest\V1\Account as GrpcAccount;
use TinkoffFinApi\Client\TinkoffFinApiClient;
use TinkoffFinApi\Resources\OperationsResource;
use TinkoffFinApi\Resources\PortfoliosResource;

class Account extends AbstractModel
{
    /** @var string Идентификатор счёта */
    public string $id = '';
    /** @var int Тип счёта */
    public int $type = 0;
    /** @var string Название счёта */
    public string $name = '';
    /** @var int Статус счёта */
    public int $status = 0;
    /** @var Carbon|null Дата открытия счёта */
    public ?Carbon $opened_date = null;
    /** @var Carbon|null Дата закрытия счёта */
    public ?Carbon $closed_date = null;
    /** @var int Уровень доступа к текущему счёту (определяется токеном) */
    public int $access_level = 0;

    /**
     * @param GrpcAccount $grpcAccount
     * @param TinkoffFinApiClient $client
     */
    public function __construct(GrpcAccount $grpcAccount, TinkoffFinApiClient $client)
    {
        parent::__construct($client);
        
        $this->id = $grpcAccount->getId();
        $this->status = $grpcAccount->getStatus();
        $this->name = $grpcAccount->getName();
        $this->opened_date = $this->makeCarbon($grpcAccount->getOpenedDate());
        $this->closed_date = $this->makeCarbon($grpcAccount->getClosedDate());
        $this->type = $grpcAccount->getType();
        $this->access_level = $grpcAccount->getAccessLevel();
    }

    /**
     * Получить операции за все время по данному счету
     *
     * @return Operation[]
     */
    public function getAllOperations(): array
    {
        return (new OperationsResource($this->client))->all(['accountId' => $this->id]);
    }

    /**
     * Получить операцию по ID, если она есть 
     * 
     * @param $id
     *
     * @return Operation|null
     */
    public function getFindByIdOperation($id): ?Operation
    {
        foreach ($this->getAllOperations() as $operation) {
            if ($operation->id === $id) {
                return $operation;
            }
        }
        
        return null;
    }

    /**
     * Получение портфеля счета 
     * 
     * @return Portfolio|null
     */
    public function getPortfolio(): ?Portfolio
    {
        return (new PortfoliosResource($this->client))->findById($this->id);
    }

    /**
     * Получение всех операций счета по срезу дат
     * 
     * @param Carbon|null $from
     * @param Carbon|null $to
     *
     * @return Operation[]
     */
    public function getByDateRangeOperations(?Carbon $from = null, ?Carbon $to = null): array
    {
        return (new OperationsResource($this->client))->getByDateRange($this->id, $from, $to);
    }
}
