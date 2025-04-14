<?php

namespace App\Models;

use App\Enums\TransactionTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaction extends Model
{
    /** @use HasFactory<\Database\Factories\TransactionFactory> */
    use HasFactory;

    protected $table = 'transactions';

    protected $fillable = [
        'type',
        'number',
        'note',
    ];

    protected $casts = [
        'type' => TransactionTypeEnum::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(TransactionDetail::class);
    }

    public function getNextNumber(): int
    {
        return ($this->query()
            ->lockForUpdate()
            ->max('number') ?? 0) + 1;
    }

    public function getByUserId(int $userId)
    {
        return self::query()
            ->select([
                'transactions.id',
                'transactions.type',
                'transactions.number',
                'transactions.note',
            ])
            ->WhereHas('details', fn($query) => $query->where('user_id', $userId))
            ->with([
                'details' => function ($query) {
                    $query->select([
                        'transaction_details.id',
                        'transaction_details.transaction_id',
                        'transaction_details.type',
                        'transaction_details.amount',
                        'transaction_details.user_id',
                        'users.name as user_name',
                    ])
                        ->join('users', 'users.id', '=', 'transaction_details.user_id');
                }
            ])
            ->paginate();
    }
}
