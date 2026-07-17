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

    public const TYPE_CUSTOMER_DEPOSIT = 'customer_deposit';

    public const TYPE_CUSTOMER_WITHDRAWAL = 'customer_withdrawal';

    public const STATUS_PENDING = 'pending';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_FAILED = 'failed';

    public const STATUS_REVERSED = 'reversed';

    public const SOURCE_ATM = 'atm';

    public const SOURCE_TRANSFER = 'transfer';

    public const SOURCE_EMPLOYEE = 'employee';

    public const SOURCE_SYSTEM = 'system';

    public const SOURCE_CUSTOMER = 'customer';

    /**
     * @return array<string, string>
     */
    public static function typeOptions(): array
    {
        return [
            self::TYPE_ATM_DEPOSIT => 'ATM deposit',
            self::TYPE_ATM_WITHDRAWAL => 'ATM withdrawal',
            self::TYPE_TRANSFER_DEBIT => 'Transfer debit',
            self::TYPE_TRANSFER_CREDIT => 'Transfer credit',
            self::TYPE_ADJUSTMENT => 'Adjustment',
            self::TYPE_CUSTOMER_DEPOSIT => 'Customer deposit',
            self::TYPE_CUSTOMER_WITHDRAWAL => 'Customer withdrawal',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function statusOptions(): array
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_FAILED => 'Failed',
            self::STATUS_REVERSED => 'Reversed',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function sourceOptions(): array
    {
        return [
            self::SOURCE_ATM => 'ATM',
            self::SOURCE_TRANSFER => 'Transfer',
            self::SOURCE_EMPLOYEE => 'Employee',
            self::SOURCE_SYSTEM => 'System',
            self::SOURCE_CUSTOMER => 'Customer',
        ];
    }

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
     * @return BelongsTo<Account, Transaction>
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

    public function typeLabel(): string
    {
        return self::typeOptions()[$this->type] ?? ucfirst(str_replace('_', ' ', $this->type));
    }

    public function statusLabel(): string
    {
        return self::statusOptions()[$this->status] ?? ucfirst($this->status);
    }

    public function sourceLabel(): string
    {
        return self::sourceOptions()[$this->source] ?? ucfirst($this->source);
    }
}
