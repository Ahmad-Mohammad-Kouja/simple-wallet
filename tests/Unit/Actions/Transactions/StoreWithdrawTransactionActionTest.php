<?php

namespace Tests\Unit\Actions;

use App\Actions\Transactions\StoreDepositTransactionAction;
use App\Actions\Transactions\StoreWithdrawTransactionAction;
use App\Enums\TransactionDetailTypeEnum;
use App\Enums\TransactionTypeEnum;
use App\Models\Balance;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\User;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class StoreWithdrawTransactionActionTest extends TestCase
{
    protected StoreWithdrawTransactionAction $storeWithdrawTransactionAction;

    public function setUp(): void
    {
        parent::setUp();
        $this->storeWithdrawTransactionAction = App::make(StoreWithdrawTransactionAction::class);
    }

    public function test_can_store_deposit_transaction_with_enough_balance()
    {
        $user = User::factory()
            ->create();

        Balance::factory()
            ->for($user)
            ->create(['amount' => 1300]);

        $storeWithdrawTransactionResult = $this->storeWithdrawTransactionAction->execute($user->id, 1200);

        $this->assertTrue($storeWithdrawTransactionResult->isSuccess());

        $this->assertDatabaseHas(Transaction::class, [
            'id' => $storeWithdrawTransactionResult->getData('transaction')->id,
            'type' => TransactionTypeEnum::WITHDRAW,
            'note' => null,
        ])
            ->assertDatabaseHas(TransactionDetail::class, [
                'transaction_id' => $storeWithdrawTransactionResult->getData('transaction')->id,
                'user_id' => $user->id,
                'type' => TransactionDetailTypeEnum::WITHDRAW,
                'amount' => 1200,
            ]);
    }

    public function test_can_store_deposit_transaction_with_enough_balance_including_transactions()
    {
        $user = User::factory()
            ->create();

        TransactionDetail::factory()
            ->for($user)
            ->create(['type' => TransactionDetailTypeEnum::WITHDRAW, 'amount' => 100]);

        TransactionDetail::factory()
            ->for($user)
            ->create(['type' => TransactionDetailTypeEnum::DEPOSIT, 'amount' => 500]);

        Balance::factory()
            ->for($user)
            ->create(['amount' => 900, 'transaction_id' => null]);

        $storeWithdrawTransactionResult = $this->storeWithdrawTransactionAction->execute($user->id, 1200);

        $this->assertTrue($storeWithdrawTransactionResult->isSuccess());

        $this->assertDatabaseHas(Transaction::class, [
            'id' => $storeWithdrawTransactionResult->getData('transaction')->id,
            'type' => TransactionTypeEnum::WITHDRAW,
            'note' => null,
        ])
            ->assertDatabaseHas(TransactionDetail::class, [
                'transaction_id' => $storeWithdrawTransactionResult->getData('transaction')->id,
                'user_id' => $user->id,
                'type' => TransactionDetailTypeEnum::WITHDRAW,
                'amount' => 1200,
            ]);
    }

    public function test_can_store_deposit_transaction_with_enough_balance_including_transactions_skipping_old_one()
    {
        $user = User::factory()
            ->create();

        $transaction = Transaction::factory()
            ->create();

        TransactionDetail::factory()
            ->for($transaction)
            ->for($user)
            ->create(['type' => TransactionDetailTypeEnum::WITHDRAW, 'amount' => 100]);

        Balance::factory()
            ->for($user)
            ->create(['amount' => 1199, 'transaction_id' => $transaction->id]);

        $newTransaction = Transaction::factory()
            ->create();

        TransactionDetail::factory()
            ->for($user)
            ->for($newTransaction)
            ->create(['type' => TransactionDetailTypeEnum::DEPOSIT, 'amount' => 12]);

        $storeWithdrawTransactionResult = $this->storeWithdrawTransactionAction->execute($user->id, 1200);

        $this->assertTrue($storeWithdrawTransactionResult->isSuccess());

        $this->assertDatabaseHas(Transaction::class, [
            'id' => $storeWithdrawTransactionResult->getData('transaction')->id,
            'type' => TransactionTypeEnum::WITHDRAW,
            'note' => null,
        ])
            ->assertDatabaseHas(TransactionDetail::class, [
                'transaction_id' => $storeWithdrawTransactionResult->getData('transaction')->id,
                'user_id' => $user->id,
                'type' => TransactionDetailTypeEnum::WITHDRAW,
                'amount' => 1200,
            ]);
    }

    public function test_can_not_store_deposit_transaction_without_enough_balance()
    {
        $user = User::factory()
            ->create();

        Balance::factory()
            ->for($user)
            ->create(['amount' => 1100]);

        $storeWithdrawTransactionResult = $this->storeWithdrawTransactionAction->execute($user->id, 1200);

        $this->assertTrue($storeWithdrawTransactionResult->isClientError());
        $this->assertEquals(__('global.response_messages.no_enough_balance'), $storeWithdrawTransactionResult->getMessage());
    }

    public function test_can_not_store_deposit_transaction_without_enough_balance_including_transactions()
    {
        $user = User::factory()
            ->create();

        Balance::factory()
            ->for($user)
            ->create(['amount' => 1300]);

        TransactionDetail::factory()
            ->for($user)
            ->create(['type' => TransactionDetailTypeEnum::WITHDRAW, 'amount' => 101]);

        $storeWithdrawTransactionResult = $this->storeWithdrawTransactionAction->execute($user->id, 1200);

        $this->assertTrue($storeWithdrawTransactionResult->isClientError());
        $this->assertEquals(__('global.response_messages.no_enough_balance'), $storeWithdrawTransactionResult->getMessage());
    }

    public function test_can_not_store_deposit_transaction_without_enough_balance_including_transactions_skipping_old_ones()
    {
        $user = User::factory()
            ->create();

        $transaction = Transaction::factory()
            ->create();

        TransactionDetail::factory()
            ->for($transaction)
            ->for($user)
            ->create(['type' => TransactionDetailTypeEnum::DEPOSIT, 'amount' => 100]);

        Balance::factory()
            ->for($user)
            ->create(['amount' => 1201, 'transaction_id' => $transaction->id]);

        $newTransaction = Transaction::factory()
            ->create();

        TransactionDetail::factory()
            ->for($user)
            ->for($newTransaction)
            ->create(['type' => TransactionDetailTypeEnum::WITHDRAW, 'amount' => 2]);

        $storeWithdrawTransactionResult = $this->storeWithdrawTransactionAction->execute($user->id, 1200);

        $this->assertTrue($storeWithdrawTransactionResult->isClientError());
        $this->assertEquals(__('global.response_messages.no_enough_balance'), $storeWithdrawTransactionResult->getMessage());
    }
}
