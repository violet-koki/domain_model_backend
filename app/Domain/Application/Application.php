<?php

namespace App\Domain\Application;

use App\Models\Application as ApplicationModel;

class Application
{
    // readonly修飾子で不変性を保証
    private readonly ?int $id;
    private readonly ?int $userId;
    private readonly ?string $examineNumber;
    private readonly ?string $attendanceNumber;
    private readonly ?bool $passFlag;
    private readonly ?bool $attendanceFlag;
    private readonly ?string $status;
    private readonly ?\DateTime $createdAt;
    private readonly ?\DateTime $updatedAt;

    /**
     * コンストラクタでエンティティを初期化
     */
    public function __construct(
        ?int $id = null,
        ?int $userId = null,
        ?string $examineNumber = null,
        ?string $attendanceNumber = null,
        ?bool $passFlag = false,
        ?bool $attendanceFlag = false,
        ?string $status = null,
        ?\DateTime $createdAt = null,
        ?\DateTime $updatedAt = null
    ) {
        $this->id = $id;
        $this->userId = $userId;
        $this->examineNumber = $examineNumber;
        $this->attendanceNumber = $attendanceNumber;
        $this->passFlag = $passFlag;
        $this->attendanceFlag = $attendanceFlag;
        $this->status = $status;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    /**
     * Laravelモデルからドメインエンティティを生成するファクトリメソッド
     * @param ApplicationModel $model Laravelのアプリケーションモデル
     * @return self
     */
    public static function fromModel(ApplicationModel $model): self
    {
        return new self(
            $model->id,
            $model->user_id,
            $model->examine_number,
            $model->attendance_number,
            (bool)$model->pass_flag,
            (bool)$model->attendance_flag,
            $model->status,
            $model->created_at ? new \DateTime($model->created_at) : null,
            $model->updated_at ? new \DateTime($model->updated_at) : null
        );
    }

    /**
     * 条件に基づいて申請を取得する静的メソッド
     *
     * @param array $conditions 検索条件
     * @return self|null
     */
    public static function fetchApplication(array $conditions): ?self
    {
        $applicationModel = ApplicationModel::where($conditions)->first();

        if (!$applicationModel) {
            return null;
        }

        return self::fromModel($applicationModel);
    }

    /**
     * ユーザーIDに基づく最新の合格申請を取得する静的メソッド
     *
     * @param int $userId ユーザーID
     * @return self|null
     */
    public static function fetchLatestPassedApplication(int $userId): ?self
    {
        $applicationModel = ApplicationModel::where('user_id', $userId)
            ->where('pass_flag', true)
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$applicationModel) {
            return null;
        }

        return self::fromModel($applicationModel);
    }

    /**
     * ユーザーIDに基づく最新の完了申請を取得する静的メソッド
     *
     * @param int $userId ユーザーID
     * @return self|null
     */
    public static function fetchLatestCompletedApplication(int $userId): ?self
    {
        $applicationModel = ApplicationModel::where('user_id', $userId)
            ->whereNotNull('completion_number')
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$applicationModel) {
            return null;
        }

        return self::fromModel($applicationModel);
    }

    /**
     * IDを取得
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * ユーザーIDを取得
     * @return int|null
     */
    public function getUserId(): ?int
    {
        return $this->userId;
    }

    /**
     * 試験番号を取得
     * @return string|null
     */
    public function getExamineNumber(): ?string
    {
        return $this->examineNumber;
    }

    /**
     * 出席番号を取得
     * @return string|null
     */
    public function getAttendanceNumber(): ?string
    {
        return $this->attendanceNumber;
    }

    /**
     * 合格フラグが有効か確認
     * @return bool
     */
    public function isPassFlagEnabled(): bool
    {
        return $this->passFlag === true;
    }

    /**
     * 出席フラグが有効か確認
     * @return bool
     */
    public function isAttendanceFlagEnabled(): bool
    {
        return $this->attendanceFlag === true;
    }

    /**
     * ステータスを取得
     * @return string|null
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * 作成日時を取得
     * @return \DateTime|null
     */
    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    /**
     * 更新日時を取得
     * @return \DateTime|null
     */
    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    /**
     * 情報を更新した新しいインスタンスを作成（イミュータブル設計のため）
     * @return self
     */
    public function withUpdatedInformation(
        ?string $examineNumber = null,
        ?string $attendanceNumber = null,
        ?bool $passFlag = null,
        ?bool $attendanceFlag = null,
        ?string $status = null
    ): self {
        return new self(
            $this->id,
            $this->userId,
            $examineNumber ?? $this->examineNumber,
            $attendanceNumber ?? $this->attendanceNumber,
            $passFlag ?? $this->passFlag,
            $attendanceFlag ?? $this->attendanceFlag,
            $status ?? $this->status,
            $this->createdAt,
            $this->updatedAt
        );
    }
}