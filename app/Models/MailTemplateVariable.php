<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $mail_template_id
 * @property int $variable_name
 **/
class MailTemplateVariable extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'mail_template_id',
        'variable_name',
    ];

    /**
     * インスタンスを生成する
     *
     * @param int $mail_template_id
     * @param string $variable_name
     * @return self
     */
    public static function create(
        int $mail_template_id,
        string $variable_name,
    ): self {
        return (new self())->fill([
            'mail_template_id' => $mail_template_id,
            'variable_name' => $variable_name,
        ]);
    }
}
