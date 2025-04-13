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

class StoreTransferTransactionAction
{
    public function __construct(
        protected User $user,
        protected Transaction $transaction,
        protected TransactionDetail $transactionDetail,
        protected Balance $balance,
        protected OperationResult $operationResult
    ) {}

    public function execute(int $sourceUserId, int $destinationUserId, float $amount)
    {
        $users = $this->user->find([$sourceUserId, $destinationUserId]);
        if ($users->count() !== 2) {
            return $this->operationResult->markAsClientError(__('global.response_messages.user_not_found'));
        }

        try {
            DB::beginTransaction();

            $balance = $this->balance->getByUserIdWithLock($sourceUserId);
            if ($balance - $amount < 0) {
                DB::rollBack();
                return $this->operationResult->markAsClientError(__('global.response_messages.no_enough_balance'));
            }

            $transaction = $this->transaction->create([
                'type' => TransactionTypeEnum::TRANSFER,
                'number' => $this->transaction->getNextNumber(),
            ]);

            $transactionDetailsData[] = [
                'transaction_id' => $transaction->id,
                'user_id' => $sourceUserId,
                'type' => TransactionDetailTypeEnum::WITHDRAW,
                'amount' => $amount,
            ];

            $transactionDetailsData[] = [
                'transaction_id' => $transaction->id,
                'user_id' => $destinationUserId,
                'type' => TransactionDetailTypeEnum::DEPOSIT,
                'amount' => $amount,
            ];

            $this->transactionDetail->insert($transactionDetailsData);
            DB::commit();

            $this->operationResult->setData($transaction, 'transaction');
        } catch (Exception $exception) {
            DB::rollBack();
            dd($exception->getMessage());
            Log::warning("error on store transfer transaction for user: {$destinationUserId} from user {$sourceUserId}, exception: {$exception->getMessage()}");
            $this->operationResult->markAsInternalServerError($exception->getMessage());
        }

        return $this->operationResult;
    }
}
