<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ATMCard extends Model
{
    use HasFactory;

    protected $table = 'atm_cards';

    public const STATUS_ACTIVE = 'active';

    public const STATUS_BLOCKED = 'blocked';

    public const STATUS_EXPIRED = 'expired';

    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'account_id',
        'atm_card_request_id',
        'card_number',
        'pin_hash',
        'status',
        'failed_attempts',
        'issued_by',
        'issued_at',
        'expires_at',
        'last_used_at',
    ];

    protected function casts(): array
    {
        return [
            'issued_at' => 'datetime',
            'expires_at' => 'datetime',
            'last_used_at' => 'datetime',
        ];
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function request(): BelongsTo
    {
        return $this->belongsTo(ATMCardRequest::class, 'atm_card_request_id');
    }

    public function issuer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by');
    }
}
