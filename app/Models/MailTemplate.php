<?php

namespace App\Models;

use App\Consts\TemplateType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use App\Models\MailTemplateVariable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Collection;

/**
 * @property int $id
 * @property string $template_name
 * @property string $ses_template_name
 * @property TemplateType $template_type
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $deleted_at
 **/

/**
 * @property int $id
 * @property string $template_name
 * @property string $ses_template_name
 * @property TemplateType $template_type
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $deleted_at
 **/
class MailTemplate extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'template_name',
        'ses_template_name',
        'template_type',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'template_type' => TemplateType::class,
    ];
}