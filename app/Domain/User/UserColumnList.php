<?php

namespace App\Domain\User;

class UserColumnList
{
    // 処理が必要なカラム
    private const SPECIAL_COLUMNS = [
        'address',
        'work_address',
        'birthday',
        'expired_date',
        'work_prefecture',
        'work_zipcode',
    ];

    // ユーザーテーブルに存在するすべてのカラム名
    private const ALL_USER_COLUMNS = [
        'certification_number',
        'name',
        'name_kana',
        'gender',
        'birthday',
        'work_name',
        'work_section',
        'work_zipcode',
        'work_prefecture',
        'work_address',
        'work_phone',
        'mail',
        'address',
        'expired_date'
    ];

    // 仮想カラム'address'に対応する実際のDBカラム
    private const ADDRESS_COLUMNS = [
        'work_zipcode',
        'work_prefecture',
        'work_address1',
        'work_address2',
        'work_building',
        'work_name',
        'work_section',
        'zipcode',
        'prefecture',
        'address1',
        'address2',
        'building',
        'send_flag'
    ];

    // 仮想カラム'work_address'に対応する実際のDBカラム
    private const WORK_ADDRESS_COLUMNS = [
        'work_address1',
        'work_address2'
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
     * テンプレート変数リストと利用可能なユーザーカラムの交差を取得し、
     * その結果からUserColumnListオブジェクトを生成
     *
     * @param array<string> $templateVariables テンプレート内の変数名リスト
     * @return self 交差したカラムを含むUserColumnListオブジェクト
     */
    public static function fromTemplateVariables(array $templateVariables): self
    {
        $intersection = array_intersect(self::ALL_USER_COLUMNS, $templateVariables);
        return new self(array_values($intersection));
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

    /**
     * データベースクエリ用のカラムリストを取得
     * 仮想カラムを実際のデータベースカラムに変換
     *
     * @return array<string> クエリ用のカラムリスト
     */
    public function toDatabaseColumns(): array
    {
        $databaseColumns = $this->columns;

        // address仮想カラム処理
        if (in_array('address', $databaseColumns)) {
            $databaseColumns = array_diff($databaseColumns, ['address']);
            $databaseColumns = array_merge($databaseColumns, self::ADDRESS_COLUMNS);
        }

        // work_address仮想カラム処理
        if (in_array('work_address', $databaseColumns)) {
            $databaseColumns = array_diff($databaseColumns, ['work_address']);
            $databaseColumns = array_merge($databaseColumns, self::WORK_ADDRESS_COLUMNS);
        }

        // 重複を除去
        return array_values(array_unique($databaseColumns));
    }
}