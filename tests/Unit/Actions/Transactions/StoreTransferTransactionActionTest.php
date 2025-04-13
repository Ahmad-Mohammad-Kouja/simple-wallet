<?php

namespace Tests\Unit\Actions;

use App\Actions\Transactions\storeTransferTransactionAction;
use App\Enums\TransactionDetailTypeEnum;
use App\Enums\TransactionTypeEnum;
use App\Models\Balance;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\User;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class StoreTransferTransactionActionTest extends TestCase
{
    protected StoreTransferTransactionAction $storeTransferTransactionAction;

    public function setUp(): void
    {
        parent::setUp();
        $this->storeTransferTransactionAction = App::make(StoreTransferTransactionAction::class);
    }

    public function test_can_store_transfer_transaction_with_enough_balance()
    {
        $users = User::factory()
            ->count(2)
            ->create();

        Balance::factory()
            ->for($users[0])
            ->create(['amount' => 1300]);

        $storeWithdrawTransactionResult = $this->storeTransferTransactionAction->execute($users[0]->id, $users[1]->id, 1200);

        $this->assertTrue($storeWithdrawTransactionResult->isSuccess());

        $this->assertDatabaseHas(Transaction::class, [
            'id' => $storeWithdrawTransactionResult->getData('transaction')->id,
            'type' => TransactionTypeEnum::TRANSFER,
            'note' => null,
        ])
            ->assertDatabaseHas(TransactionDetail::class, [
                'transaction_id' => $storeWithdrawTransactionResult->getData('transaction')->id,
                'user_id' => $users[0]->id,
                'type' => TransactionDetailTypeEnum::WITHDRAW,
                'amount' => 1200,
            ])
            ->assertDatabaseHas(TransactionDetail::class, [
                'transaction_id' => $storeWithdrawTransactionResult->getData('transaction')->id,
                'user_id' => $users[1]->id,
                'type' => TransactionDetailTypeEnum::DEPOSIT,
                'amount' => 1200,
            ]);
    }

    public function test_can_store_transfer_transaction_with_enough_balance_including_transactions()
    {
        $users = User::factory()
            ->count(2)
            ->create();

        TransactionDetail::factory()
            ->for($users[0])
            ->create(['type' => TransactionDetailTypeEnum::WITHDRAW, 'amount' => 100]);

        TransactionDetail::factory()
            ->for($users[0])
            ->create(['type' => TransactionDetailTypeEnum::DEPOSIT, 'amount' => 500]);

        Balance::factory()
            ->for($users[0])
            ->create(['amount' => 900, 'transaction_id' => null]);

        $storeWithdrawTransactionResult = $this->storeTransferTransactionAction->execute($users[0]->id, $users[1]->id, 1200);

        $this->assertTrue($storeWithdrawTransactionResult->isSuccess());

        $this->assertDatabaseHas(Transaction::class, [
            'id' => $storeWithdrawTransactionResult->getData('transaction')->id,
            'type' => TransactionTypeEnum::TRANSFER,
            'note' => null,
        ])
            ->assertDatabaseHas(TransactionDetail::class, [
                'transaction_id' => $storeWithdrawTransactionResult->getData('transaction')->id,
                'user_id' => $users[0]->id,
                'type' => TransactionDetailTypeEnum::WITHDRAW,
                'amount' => 1200,
            ])
            ->assertDatabaseHas(TransactionDetail::class, [
                'transaction_id' => $storeWithdrawTransactionResult->getData('transaction')->id,
                'user_id' => $users[1]->id,
                'type' => TransactionDetailTypeEnum::DEPOSIT,
                'amount' => 1200,
            ]);
    }

    public function test_can_store_transfer_transaction_with_enough_balance_including_transactions_skipping_old_one()
    {
        $users = User::factory()
            ->count(2)
            ->create();

        $transaction = Transaction::factory()
            ->create();

        TransactionDetail::factory()
            ->for($transaction)
            ->for($users[0])
            ->create(['type' => TransactionDetailTypeEnum::WITHDRAW, 'amount' => 100]);

        Balance::factory()
            ->for($users[0])
            ->create(['amount' => 1199, 'transaction_id' => $transaction->id]);

        $newTransaction = Transaction::factory()
            ->create();

        TransactionDetail::factory()
            ->for($users[0])
            ->for($newTransaction)
            ->create(['type' => TransactionDetailTypeEnum::DEPOSIT, 'amount' => 12]);

        $storeWithdrawTransactionResult = $this->storeTransferTransactionAction->execute($users[0]->id, $users[1]->id, 1200);

        $this->assertTrue($storeWithdrawTransactionResult->isSuccess());

        $this->assertDatabaseHas(Transaction::class, [
            'id' => $storeWithdrawTransactionResult->getData('transaction')->id,
            'type' => TransactionTypeEnum::TRANSFER,
            'note' => null,
        ])
            ->assertDatabaseHas(TransactionDetail::class, [
                'transaction_id' => $storeWithdrawTransactionResult->getData('transaction')->id,
                'user_id' => $users[0]->id,
                'type' => TransactionDetailTypeEnum::WITHDRAW,
                'amount' => 1200,
            ])
            ->assertDatabaseHas(TransactionDetail::class, [
                'transaction_id' => $storeWithdrawTransactionResult->getData('transaction')->id,
                'user_id' => $users[1]->id,
                'type' => TransactionDetailTypeEnum::DEPOSIT,
                'amount' => 1200,
            ]);
    }

    public function test_can_not_store_transfer_transaction_without_enough_balance()
    {
        $users = User::factory()
            ->count(2)
            ->create();

        Balance::factory()
            ->for($users[0])
            ->create(['amount' => 1100]);

        $storeWithdrawTransactionResult = $this->storeTransferTransactionAction->execute($users[0]->id, $users[1]->id, 1200);

        $this->assertTrue($storeWithdrawTransactionResult->isClientError());
        $this->assertEquals(__('global.response_messages.no_enough_balance'), $storeWithdrawTransactionResult->getMessage());
    }

    public function test_can_not_store_transfer_transaction_without_enough_balance_including_transactions()
    {
        $users = User::factory()
            ->count(2)
            ->create();

        Balance::factory()
            ->for($users[0])
            ->create(['amount' => 1300]);

        TransactionDetail::factory()
            ->for($users[0])
            ->create(['type' => TransactionDetailTypeEnum::WITHDRAW, 'amount' => 101]);

        $storeWithdrawTransactionResult = $this->storeTransferTransactionAction->execute($users[0]->id, $users[1]->id, 1200);

        $this->assertTrue($storeWithdrawTransactionResult->isClientError());
        $this->assertEquals(__('global.response_messages.no_enough_balance'), $storeWithdrawTransactionResult->getMessage());
    }

    public function test_can_not_store_transfer_transaction_without_enough_balance_including_transactions_skipping_old_ones()
    {
        $users = User::factory()
            ->count(2)
            ->create();

        $transaction = Transaction::factory()
            ->create();

        TransactionDetail::factory()
            ->for($transaction)
            ->for($users[0])
            ->create(['type' => TransactionDetailTypeEnum::DEPOSIT, 'amount' => 100]);

        Balance::factory()
            ->for($users[0])
            ->create(['amount' => 1201, 'transaction_id' => $transaction->id]);

        $newTransaction = Transaction::factory()
            ->create();

        TransactionDetail::factory()
            ->for($users[0])
            ->for($newTransaction)
            ->create(['type' => TransactionDetailTypeEnum::WITHDRAW, 'amount' => 2]);

        $storeWithdrawTransactionResult = $this->storeTransferTransactionAction->execute($users[0]->id, $users[1]->id, 1200);

        $this->assertTrue($storeWithdrawTransactionResult->isClientError());
        $this->assertEquals(__('global.response_messages.no_enough_balance'), $storeWithdrawTransactionResult->getMessage());
    }
}
