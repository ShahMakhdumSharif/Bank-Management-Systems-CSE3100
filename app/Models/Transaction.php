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

    public const TYPE_ATM_DEPOSIT = 'atm_deposit';

    public const TYPE_ATM_WITHDRAWAL = 'atm_withdrawal';

    public const TYPE_TRANSFER_DEBIT = 'transfer_debit';

    public const TYPE_TRANSFER_CREDIT = 'transfer_credit';

    public const TYPE_ADJUSTMENT = 'adjustment';

    public const STATUS_PENDING = 'pending';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_FAILED = 'failed';

    public const STATUS_REVERSED = 'reversed';

    public const SOURCE_ATM = 'atm';

    public const SOURCE_TRANSFER = 'transfer';

    public const SOURCE_EMPLOYEE = 'employee';

    public const SOURCE_SYSTEM = 'system';

    protected $fillable = [
        'account_id',
        'related_account_id',
        'transfer_request_id',
        'reference',
        'type',
        'amount',
        'balance_before',
        'balance_after',
        'status',
        'source',
        'description',
        'handled_by',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'balance_before' => 'decimal:2',
            'balance_after' => 'decimal:2',
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
    public function relatedAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'related_account_id');
    }

    /**
     * @return BelongsTo<TransferRequest, Transaction>
     */
    public function transferRequest(): BelongsTo
    {
        return $this->belongsTo(TransferRequest::class);
    }

    /**
     * @return BelongsTo<User, Transaction>
     */
    public function handler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handled_by');
    }
}
