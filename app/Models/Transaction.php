<?php

namespace App\Models;

use App\Enums\TransactionTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    public function getNextNumber(): int
    {
        return ($this->query()
            ->lockForUpdate()
            ->max('number') ?? 0) + 1;
    }
}
