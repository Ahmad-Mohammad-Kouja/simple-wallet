<?php


namespace App\Actions\Transactions;

use App\Enums\TransactionDetailTypeEnum;
use App\Enums\TransactionTypeEnum;
use App\Helpers\OperationResult;
use App\Models\Balance;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StoreDepositTransactionAction
{
    public function __construct(
        protected User $user,
        protected Transaction $transaction,
        protected TransactionDetail $transactionDetail,
        protected Balance $balance,
        protected OperationResult $operationResult
    ) {}

    public function execute(int $userId, float $amount)
    {
        try {
            DB::beginTransaction();
            $transaction = $this->transaction->create([
                'type' => TransactionTypeEnum::DEPOSIT,
                'number' => $this->transaction->getNextNumber(),
            ]);

            $this->transactionDetail->create([
                'transaction_id' => $transaction->id,
                'user_id' => $userId,
                'type' => TransactionDetailTypeEnum::DEPOSIT,
                'amount' => $amount,
            ]);
            DB::commit();

            $this->operationResult->setData($transaction, 'transaction');
        } catch (Exception $exception) {
            DB::rollBack();
            Log::warning("error on store deposit transaction for user: {$userId}, exception: {$exception->getMessage()}");
            $this->operationResult->markAsInternalServerError($exception->getMessage());
        }

        return $this->operationResult;
    }
}
