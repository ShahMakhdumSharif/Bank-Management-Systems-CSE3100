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

    protected $fillable = [
        'employee_id',
        'subject_user_id',
        'branch_id',
        'action_type',
        'description',
        'metadata',
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

    /**
     * @return BelongsTo<User, EmployeeAction>
     */
    public function subjectUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'subject_user_id');
    }

    /**
     * @return BelongsTo<Branch, EmployeeAction>
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
}
