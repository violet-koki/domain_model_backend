<?php

namespace App\Domain\User;


class UserColumnList
{
    // 特殊な処理が必要なカラム
    private const SPECIAL_COLUMNS = [
        'address',
        'work_address',
        'birthday',
        'doctor_registration_date',
        'expired_date',
        'work_prefecture',
        'work_zipcode',
        'gender'
    ];

    /** @var array<string> ユーザーカラムのリスト */
    private array $columns;

    /**
     * @param array<string> $columns ユーザーカラムのリスト
     */
    public function __construct(array $columns)
    {
        $this->columns = $columns;
    }

    /**
     * 空のカラムリストを生成
     */
    public static function empty(): self
    {
        return new self([]);
    }

    /**
     * 指定したカラムリストから新しいインスタンスを生成
     */
    public static function fromArray(array $columns): self
    {
        return new self($columns);
    }

    /**
     * カラムリストが空かどうか
     */
    public function isEmpty(): bool
    {
        return empty($this->columns);
    }

    /**
     * 住所カラムを含むかどうか
     */
    public function hasAddress(): bool
    {
        return in_array('address', $this->columns);
    }

    /**
     * 勤務先住所カラムを含むかどうか
     */
    public function hasWorkAddress(): bool
    {
        return in_array('work_address', $this->columns);
    }

    /**
     * 誕生日カラムを含むかどうか
     */
    public function hasBirthday(): bool
    {
        return in_array('birthday', $this->columns);
    }

    /**
     * 医師登録日カラムを含むかどうか
     */
    public function hasDoctorRegistrationDate(): bool
    {
        return in_array('doctor_registration_date', $this->columns);
    }

    /**
     * 期限日カラムを含むかどうか
     */
    public function hasExpiredDate(): bool
    {
        return in_array('expired_date', $this->columns);
    }

    /**
     * 勤務先都道府県カラムを含むかどうか
     */
    public function hasWorkPrefecture(): bool
    {
        return in_array('work_prefecture', $this->columns);
    }

    /**
     * 勤務先郵便番号カラムを含むかどうか
     */
    public function hasWorkZipcode(): bool
    {
        return in_array('work_zipcode', $this->columns);
    }

    /**
     * 性別カラムを含むかどうか
     */
    public function hasGender(): bool
    {
        return in_array('gender', $this->columns);
    }

    /**
     * 特殊な処理が不要な標準カラムのリストを取得
     * 
     * @return array<string> 標準カラムのリスト
     */
    public function getStandardColumns(): array
    {
        return array_filter($this->columns, function ($column) {
            return !in_array($column, self::SPECIAL_COLUMNS);
        });
    }

    /**
     * すべてのカラムを取得
     * 
     * @return array<string> すべてのカラムのリスト
     */
    public function getAllColumns(): array
    {
        return $this->columns;
    }
}
