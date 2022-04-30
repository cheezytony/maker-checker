<?php

namespace App\Models;

use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Action extends Model
{
    use HasFactory;
    use Searchable;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'admin_id',
        'user_id',
        'authenticator_id',
        'data',
        'type',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'data' => 'object',
    ];

    /**
     * Columns that are filterable using request query strings.
     *
     * @var array<int, string>
     */
    protected array $filterableColumns = [
        'admin_id',
        'user_id',
        'authenticator_id',
        'type',
        'status',
    ];

    /**
     * Possible values for action status.
     *
     * @var array<string, string>
     */
    public const STATUS = [
        'ACCEPTED' => 'accepted',
        'REJECTED' => 'rejected',
        'PENDING' => 'pending',
    ];

    /**
     * Possible values for action type.
     *
     * @var array<string, string>
     */
    public const TYPE = [
        'CREATE' => 'create',
        'DELETE' => 'delete',
        'UPDATE' => 'update',
    ];

    /**
     * Get the admin who created the action.
     *
     * @return BelongsTo
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }

    /**
     * Get the admin who approved/rejected the action.
     *
     * @return BelongsTo
     */
    public function authenticator(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'authenticator_id');
    }

    /**
     * Get the user being acted on.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
