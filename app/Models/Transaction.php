<?php

namespace App\Models;

use Database\Factories\TransactionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    /** @use HasFactory<TransactionFactory> */
    use HasFactory;

    public const TYPE_DEPOSIT = 'deposit';

    public const TYPE_WITHDRAWAL = 'withdrawal';

    public const TYPE_TRANSFER_IN = 'transfer_in';

    public const TYPE_TRANSFER_OUT = 'transfer_out';

    protected $fillable = [
        'account_id',
        'performed_by',
        'transaction_number',
        'type',
        'amount',
        'balance_after',
        'reference',
        'description',
        'occurred_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'balance_after' => 'decimal:2',
            'occurred_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Account, Transaction>
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * @return BelongsTo<User, Transaction>
     */
    public function performer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by');
    }
}
