<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Login extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'admin_id',
        'status'
    ];

    /**
     * Possible values for login status.
     *
     * @var array<string, string>
     */
    public const STATUS = [
        'AUTHENTICATED' => 'authenticated',
        'EXCEPTION' => 'exception',
        'UNAUTHENTICATED' => 'unauthenticated',
    ];
}
