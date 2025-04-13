<?php

namespace App\Models;

use App\Enums\TransactionDetailTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class TransactionDetail extends Model
{
    /** @use HasFactory<\Database\Factories\TransactionDetailFactory> */
    use HasFactory;

    protected $table = 'transaction_details';

    protected $fillable = [
        'transaction_id',
        'user_id',
        'type',
        'amount',
    ];

    protected $casts = [
        'type' => TransactionDetailTypeEnum::class,
        'amount' => 'decimal:2'
    ];

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getUserBalanceAfterTransactionId(int $userId, ?int $transactionId = null): float
    {
        $transactions = self::query()
            ->select([
                DB::raw('SUM(amount) as amount'),
                'transaction_details.type',
            ])
            ->where('transaction_details.user_id', $userId)
            ->when($transactionId !== null, fn($query) => $query->where('transaction_details.transaction_id', '>', $transactionId))
            ->groupBy('transaction_details.type')
            ->get();

        return ($transactions->firstWhere('type', TransactionDetailTypeEnum::DEPOSIT->value)?->amount ?? 0)
            - ($transactions->firstWhere('type', TransactionDetailTypeEnum::WITHDRAW->value)?->amount ?? 0);
    }
}
