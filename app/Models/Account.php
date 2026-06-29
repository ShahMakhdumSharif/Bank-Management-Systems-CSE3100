<?php

namespace App\Models;

use Database\Factories\AccountFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'customer_id',
        'branch_id',
        'account_number',
        'type',
        'status',
        'balance',
        'freeze_reason',
        'frozen_at',
    ];

    protected function casts(): array
    {
        return [
            'balance' => 'decimal:2',
            'frozen_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<User, Account>
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
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

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function isFrozen(): bool
    {
        return $this->status === self::STATUS_FROZEN;
    }
}
