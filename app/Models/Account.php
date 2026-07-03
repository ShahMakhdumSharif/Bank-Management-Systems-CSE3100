<?php

namespace App\Models;

use Database\Factories\AccountFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Account extends Model
{
    /** @use HasFactory<AccountFactory> */
    use HasFactory;

    public const TYPE_SAVINGS = 'savings';

    public const TYPE_CURRENT = 'current';

    public const STATUS_ACTIVE = 'active';

    public const STATUS_FROZEN = 'frozen';

    public const STATUS_CLOSED = 'closed';

    protected $fillable = [
        'user_id',
        'branch_id',
        'account_number',
        'account_type',
        'balance',
        'status',
        'approved_by',
        'frozen_by',
        'approved_at',
        'frozen_at',
        'freeze_reason',
    ];

    protected function casts(): array
    {
        return [
            'balance' => 'decimal:2',
            'approved_at' => 'datetime',
            'frozen_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<User, Account>
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return BelongsTo<User, Account>
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * @return BelongsTo<User, Account>
     */
    public function freezer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'frozen_by');
    }

    /**
     * @return BelongsTo<Branch, Account>
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * @return HasMany<Transaction>
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * @return HasMany<TransferRequest>
     */
    public function outgoingTransferRequests(): HasMany
    {
        return $this->hasMany(TransferRequest::class, 'sender_account_id');
    }

    /**
     * @return HasMany<TransferRequest>
     */
    public function incomingTransferRequests(): HasMany
    {
        return $this->hasMany(TransferRequest::class, 'receiver_account_id');
    }

    /**
     * @return HasMany<ATMCardRequest>
     */
    public function atmCardRequests(): HasMany
    {
        return $this->hasMany(ATMCardRequest::class);
    }

    /**
     * @return HasMany<ATMCard>
     */
    public function atmCards(): HasMany
    {
        return $this->hasMany(ATMCard::class);
    }

    /**
     * @return MorphMany<EmployeeAction>
     */
    public function subjectActions(): MorphMany
    {
        return $this->morphMany(EmployeeAction::class, 'subject');
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function isFrozen(): bool
    {
        return $this->status === self::STATUS_FROZEN;
    }
}
