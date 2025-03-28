<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property int $year
 * @property string $receipt_number
 * @property string|null $examine_number
 * @property string $passed_examine_number
 * @property string|null $attendance_number
 * @property Carbon|null $finish_date
 * @property Carbon|string|null $examine_date
 * @property boolean|null $examine_flag
 * @property boolean|null $pass_flag
 */
class Application extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'year',
        'receipt_number',
        'attendance_flag',
        'examine_number',
        'examine_flag',
        'application_date',
        'payment_date',
        'examine_date',
        'pass_flag',
    ];

    protected $casts = [
        'attendance_flag' => 'boolean',
        'examine_flag' => 'boolean',
        'pass_flag' => 'boolean',
        'application_date' => 'datetime',
        'examine_date' => 'datetime',
    ];

    /**
     * 申し込みに対応するユーザを取得する
     * Application:user_id -> User: id
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}