<?php

namespace App\Domain\Application;

/**
 * アプリケーションに関連するカラムリストを表すバリューオブジェクト
 * 
 * このクラスはEmailテンプレートで使用されるアプリケーション関連のカラムを管理します。
 * 特殊な処理が必要なカラム（passed_examine_number, attendance_numberなど）と標準的なカラムを区別します。
 */
class ApplicationColumnList
{
    // 特殊な処理が必要なカラム
    private const SPECIAL_COLUMNS = [
        'passed_examine_number',
        'attendance_number'
    ];

    // アプリケーションテーブルに存在するすべてのカラム名
    private const ALL_APPLICATION_COLUMNS = [
        'receipt_number',
        'examine_number',
        'passed_examine_number',
        'attendance_number'
    ];

    /** @var array<string> アプリケーションカラムのリスト */
    private array $columns;

    /**
     * @param array<string> $columns アプリケーションカラムのリスト
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
     * テンプレート変数リストと利用可能なアプリケーションカラムの交差を取得し、
     * その結果からApplicationColumnListオブジェクトを生成
     * 
     * @param array<string> $templateVariables テンプレート内の変数名リスト
     * @return self 交差したカラムを含むApplicationColumnListオブジェクト
     */
    public static function fromTemplateVariables(array $templateVariables): self
    {
        $intersection = array_intersect(self::ALL_APPLICATION_COLUMNS, $templateVariables);
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
     * 合格試験番号カラムを含むかどうか
     */
    public function hasPassedExamineNumber(): bool
    {
        return in_array('passed_examine_number', $this->columns);
    }

    /**
     * 出席番号カラムを含むかどうか
     */
    public function hasAttendanceNumber(): bool
    {
        return in_array('attendance_number', $this->columns);
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
