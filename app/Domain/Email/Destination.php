<?php

namespace App\Domain\Email;

use App\Domain\User\UserColumnList;
use App\Consts\DestinationType;

/**
 * メール送信先を表すドメインモデル
 */
class Destination
{
    /**
     * @param DestinationType $type 送信先のタイプ
     * @param array $targets 対象IDや番号のリスト
     * @param UserColumnList $userColumns 取得するユーザーのカラムリスト
     */
    public function __construct(
        private readonly DestinationType $type,
        private readonly array $targets,
        private readonly UserColumnList $userColumns
    ) {}

    /**
     * リクエストデータから送信先オブジェクトを生成
     *
     * @param array $requestData リクエストデータ
     * @param UserColumnList $userColumns 取得するユーザーのカラムリスト
     * @return self
     */
    public static function fromRequest(array $requestData, UserColumnList $userColumns): self
    {


        $type = DestinationType::from($requestData['destination_type']);
        $targets = $requestData['targets'] ?? [];

        return new self($type, $targets, $userColumns);
    }

    /**
     * 送信先タイプを取得
     *
     * @return DestinationType
     */
    public function getType(): DestinationType
    {
        return $this->type;
    }

    /**
     * 対象IDや番号のリストを取得
     *
     * @return array
     */
    public function getTargets(): array
    {
        return $this->targets;
    }

    /**
     * ユーザーカラムリストを取得
     *
     * @return UserColumnList
     */
    public function getUserColumns(): UserColumnList
    {
        return $this->userColumns;
    }

    /**
     * ユーザーカラムの配列を取得
     *
     * @return array
     */
    public function getUserColumnsArray(): array
    {
        return $this->userColumns->getAllColumns();
    }
}