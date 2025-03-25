<?php

declare(strict_types=1);

namespace App\Consts;

enum DestinationType: int
{
    case UserID = 0;
    case CertificationNumber = 1;

    /** 
     * 名前を取得
     * 
     * @return string
     */
    public function description(): string
    {
        return match ($this) {
            self::UserID => 'ユーザID',
            self::CertificationNumber => '認定番号',
        };
    }
}
