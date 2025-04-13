<?php

namespace App\Http\Controllers\API;

use App\Actions\Transactions\StoreDepositTransactionAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Wallets\StoreDepositRequest;

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
}
