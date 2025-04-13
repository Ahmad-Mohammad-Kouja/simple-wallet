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

class StoreWithdrawTransactionAction
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

            $balance = $this->balance->getByUserIdWithLock($userId);

            if ($balance - $amount < 0) {
                DB::rollBack();
                return $this->operationResult->markAsClientError(__('global.response_messages.no_enough_balance'));
            }

            $transaction = $this->transaction->create([
                'type' => TransactionTypeEnum::WITHDRAW,
                'number' => $this->transaction->getNextNumber(),
            ]);

            $this->transactionDetail->create([
                'transaction_id' => $transaction->id,
                'user_id' => $userId,
                'type' => TransactionDetailTypeEnum::WITHDRAW,
                'amount' => $amount,
            ]);
            DB::commit();

            $this->operationResult->setData($transaction, 'transaction');
        } catch (Exception $exception) {
            DB::rollBack();
            dd($exception->getMessage());
            Log::warning("error on store deposit transaction for user: {$userId}, exception: {$exception->getMessage()}");
            $this->operationResult->markAsInternalServerError($exception->getMessage());
        }

        return $this->operationResult;
    }
}
