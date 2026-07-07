<?php

namespace App\Models;

use Database\Factories\EmployeeActionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeAction extends Model
{
    /** @use HasFactory<EmployeeActionFactory> */
    use HasFactory;

    public const TYPE_CUSTOMER_APPROVED = 'customer_approved';

    public const TYPE_CUSTOMER_REJECTED = 'customer_rejected';

    public const TYPE_ACCOUNT_FROZEN = 'account_frozen';

    public const TYPE_ACCOUNT_UNFROZEN = 'account_unfrozen';

    public const TYPE_TRANSFER_APPROVED = 'transfer_approved';

    public const TYPE_TRANSFER_REJECTED = 'transfer_rejected';

    protected $fillable = [
        'employee_id',
        'action_type',
        'subject_type',
        'subject_id',
        'description',
        'metadata',
        'ip_address',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
        ];
    }

    /**
     * @return BelongsTo<User, EmployeeAction>
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    public function subject()
    {
        return $this->morphTo();
    }
}
