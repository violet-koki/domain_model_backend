<?php

declare(strict_types=1);

namespace App\Consts;

enum TemplateType: int
{
    case System = 1;
    case BatchSending = 2;

    /**
     * 名前を取得
     *
     * @return string
     */
    public function description(): string
    {
        dd('test');
        return match ($this) {
            self::System => 'システム',
            self::BatchSending => '一斉送信',
        };
    }
}
