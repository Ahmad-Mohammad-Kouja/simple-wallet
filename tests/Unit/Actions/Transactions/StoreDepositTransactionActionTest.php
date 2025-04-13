<?php

namespace Tests\Unit\Actions;

use App\Actions\Transactions\StoreDepositTransactionAction;
use App\Enums\TransactionDetailTypeEnum;
use App\Enums\TransactionTypeEnum;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\User;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class StoreDepositTransactionActionTest extends TestCase
{
    protected StoreDepositTransactionAction $storeDepositTransactionAction;

    public function setUp(): void
    {
        parent::setUp();
        $this->storeDepositTransactionAction = App::make(StoreDepositTransactionAction::class);
    }

    public function test_can_store_deposit_transaction()
    {
        $user = User::factory()
            ->create();

        $storeDepositTransactionResult = $this->storeDepositTransactionAction->execute($user->id, 1200);

        $this->assertTrue($storeDepositTransactionResult->isSuccess());

        $this->assertDatabaseHas(Transaction::class, [
            'id' => $storeDepositTransactionResult->getData('transaction')->id,
            'type' => TransactionTypeEnum::DEPOSIT,
            'note' => null,
        ])
            ->assertDatabaseHas(TransactionDetail::class, [
                'transaction_id' => $storeDepositTransactionResult->getData('transaction')->id,
                'user_id' => $user->id,
                'type' => TransactionDetailTypeEnum::DEPOSIT,
                'amount' => 1200,
            ]);
    }
}
