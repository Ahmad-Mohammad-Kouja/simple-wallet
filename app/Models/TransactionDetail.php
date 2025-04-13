<?php

namespace App\Models;

use App\Enums\TransactionDetailTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
}
