<?php

namespace App\Http\Controllers\API;

use App\Actions\Transactions\StoreDepositTransactionAction;
use App\Actions\Transactions\StoreWithdrawTransactionAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Wallets\StoreDepositRequest;
use App\Http\Requests\Api\Wallets\StoreWithdrawRequest;

class WalletController extends Controller
{
    public function __construct() {}

    public function deposit(StoreDepositRequest $request, StoreDepositTransactionAction $storeDepositTransactionAction)
    {
        $storeDepositTransactionResult = $storeDepositTransactionAction->execute(userId: $request->get('user_id'), amount: $request->float('amount'));

        if ($storeDepositTransactionResult->isFail()) {
            return $this->operationResultFail($storeDepositTransactionResult);
        }

        return $this->successResponse([]);
    }

    public function withdraw(StoreWithdrawRequest $request, StoreWithdrawTransactionAction $storeWithdrawTransactionAction)
    {
        $storeWithdrawTransactionResult = $storeWithdrawTransactionAction->execute(userId: $request->get('user_id'), amount: $request->float('amount'));

        if ($storeWithdrawTransactionResult->isFail()) {
            return $this->operationResultFail($storeWithdrawTransactionResult);
        }

        return $this->successResponse([]);
    }
}
