<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Transactions\GetTransactionsRequest;
use App\Http\Resources\Api\Transactions\TransactionResource;
use App\Models\Transaction;

class TransactionController extends Controller
{
    public function __construct(protected Transaction $transaction) {}

    public function index(GetTransactionsRequest $request)
    {
        return TransactionResource::collection($this->transaction->getByUserId($request->get('user_id')))
            ->additional(['message' => __('global.response_messages.ok')]);
    }
}
