<?php

namespace TinkoffFinApi\Tests\Account\Unit;

use PHPUnit\Framework\TestCase;
use TinkoffFinApi\Client\TinkoffFinApiClient;
use TinkoffFinApi\Resources\AccountsResource;

class AccountsTest extends TestCase
{
    public function testAllAccounts()
    {
        $client = new TinkoffFinApiClient('fake-token');
        $accountsResource = new AccountsResource($client);

        // Мокаем grpcClient->GetAccounts(...) и т.п.
        // Или просто проверяем, что вызывается нужный метод
        $this->assertIsArray($accountsResource->all());
    }
}
