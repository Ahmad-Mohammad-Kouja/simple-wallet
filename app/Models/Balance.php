<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\App;

class Balance extends Model
{
    /** @use HasFactory<\Database\Factories\BalanceFactory> */
    use HasFactory;

    protected $table = 'balances';

    protected $fillable = [
        'user_id',
        'transaction_id',
        'amount',
    ];

    protected $casts = [
        'amount' => 'decimal:2'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function getByUserId(int $userId): float
    {
        $balance = self::query()
            ->where('user_id', $userId)
            ->latest()
            ->first();

        $transactionBalance = App::make(TransactionDetail::class)
            ->getUserBalanceAfterTransactionId($userId, $balance->transaction_id ?? null);

        return $transactionBalance + $balance->amount;
    }

    public function getByUserIdWithLock(int $userId): float
    {
        $balance = self::query()
            ->where('user_id', $userId)
            ->latest()
            ->lockForUpdate()
            ->first();

        $transactionBalance = App::make(TransactionDetail::class)
            ->getUserBalanceAfterTransactionId($userId, $balance?->transaction_id ?? null);

        return $transactionBalance + $balance->amount;
    }
}
