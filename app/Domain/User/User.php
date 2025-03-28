<?php

namespace App\Domain\User;

class User
{
    // readonly 修飾子を使用して不変性を保証
    private readonly ?int $id;
    private readonly ?string $name;
    private readonly ?string $email;
    private readonly ?string $address;
    // 勤務先住所を2つのフィールドで管理
    private readonly ?string $workAddress1;
    private readonly ?string $workAddress2;
    private readonly ?string $birthday;
    private readonly ?string $expiredDate;
    private readonly ?string $workPrefecture;
    private readonly ?string $workZipcode;

    /**
     * コンストラクタでエンティティを初期化
     */
    public function __construct(
        ?int $id = null,
        ?string $name = null,
        ?string $email = null,
        ?string $address = null,
        ?string $workAddress1 = null,
        ?string $workAddress2 = null,
        ?string $birthday = null,
        ?string $expiredDate = null,
        ?string $workPrefecture = null,
        ?string $workZipcode = null
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->address = $address;
        $this->workAddress1 = $workAddress1;
        $this->workAddress2 = $workAddress2;
        $this->birthday = $birthday;
        $this->expiredDate = $expiredDate;
        $this->workPrefecture = $workPrefecture;
        $this->workZipcode = $workZipcode;
    }

    /**
     * Laravelモデルからドメインエンティティを生成するファクトリメソッド
     * @param \App\Models\User $model Laravelのユーザーモデル
     * @return self
     */
    public static function fromModel(\App\Models\User $model): self
    {
        return new self(
            $model->id,
            $model->name,
            $model->email,
            $model->address,
            $model->work_address1,
            $model->work_address2,
            $model->birthday,
            $model->expired_date,
            $model->work_prefecture,
            $model->work_zipcode
        );
    }

    /**
     * フォーマット済みの住所を取得
     * @return string
     */
    public function getFormattedAddress(): string
    {
        // 住所のフォーマット処理（実際の要件に応じて実装）
        if ($this->address === null) {
            return '';
        }

        return $this->address;
    }

    /**
     * フォーマット済みの勤務先住所を取得
     * @return string
     */
    public function getFormattedWorkAddress(): string
    {
        // 勤務先住所のフォーマット処理
        if ($this->workAddress1 === null && $this->workAddress2 === null) {
            return '';
        }

        $address1 = $this->workAddress1 ?? '';
        $address2 = $this->workAddress2 ?? '';

        return $address1 . $address2;
    }

    /**
     * フォーマット済みの生年月日を取得
     * @return string
     */
    public function getFormattedBirth(): string
    {
        // 生年月日のフォーマット処理（例：YYYY年MM月DD日）
        if ($this->birthday === null) {
            return '';
        }

        // 日付形式の変換処理（例）
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $this->birthday)) {
            $date = new \DateTime($this->birthday);
            return $date->format('Y年m月d日');
        }

        return $this->birthday;
    }

    /**
     * フォーマット済みの有効期限を取得
     * @return string
     */
    public function getFormattedExpiredDate(): string
    {
        // 有効期限のフォーマット処理
        if ($this->expiredDate === null) {
            return '';
        }

        // 日付形式の変換処理（例）
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $this->expiredDate)) {
            $date = new \DateTime($this->expiredDate);
            return $date->format('Y年m月d日');
        }

        return $this->expiredDate;
    }

    /**
     * フォーマット済みの勤務先都道府県を取得
     * @return string
     */
    public function getFormattedWorkPrefecture(): string
    {
        // 勤務先都道府県のフォーマット処理
        if ($this->workPrefecture === null) {
            return '';
        }

        return $this->workPrefecture;
    }

    /**
     * フォーマット済みの勤務先郵便番号を取得
     * @return string
     */
    public function getFormattedWorkZipCode(): string
    {
        // 勤務先郵便番号のフォーマット処理（例：XXX-XXXX形式）
        if ($this->workZipcode === null) {
            return '';
        }

        // ハイフンなしの場合はハイフン付きに変換（例）
        if (preg_match('/^(\d{3})(\d{4})$/', $this->workZipcode, $matches)) {
            return $matches[1] . '-' . $matches[2];
        }

        return $this->workZipcode;
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
     * 名前を取得
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * メールアドレスを取得
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * 住所を取得
     * @return string|null
     */
    public function getAddress(): ?string
    {
        return $this->address;
    }

    /**
     * 勤務先住所1を取得
     * @return string|null
     */
    public function getWorkAddress1(): ?string
    {
        return $this->workAddress1;
    }

    /**
     * 勤務先住所2を取得
     * @return string|null
     */
    public function getWorkAddress2(): ?string
    {
        return $this->workAddress2;
    }

    /**
     * 生年月日を取得
     * @return string|null
     */
    public function getBirthday(): ?string
    {
        return $this->birthday;
    }

    /**
     * 有効期限を取得
     * @return string|null
     */
    public function getExpiredDate(): ?string
    {
        return $this->expiredDate;
    }

    /**
     * 勤務先都道府県を取得
     * @return string|null
     */
    public function getWorkPrefecture(): ?string
    {
        return $this->workPrefecture;
    }

    /**
     * 勤務先郵便番号を取得
     * @return string|null
     */
    public function getWorkZipcode(): ?string
    {
        return $this->workZipcode;
    }

    /**
     * ユーザー情報を更新した新しいインスタンスを作成（イミュータブル設計のため）
     * @return self
     */
    public function withUpdatedInformation(
        ?string $name = null,
        ?string $email = null,
        ?string $address = null,
        ?string $workAddress1 = null,
        ?string $workAddress2 = null,
        ?string $birthday = null,
        ?string $expiredDate = null,
        ?string $workPrefecture = null,
        ?string $workZipcode = null
    ): self {
        return new self(
            $this->id,
            $name ?? $this->name,
            $email ?? $this->email,
            $address ?? $this->address,
            $workAddress1 ?? $this->workAddress1,
            $workAddress2 ?? $this->workAddress2,
            $birthday ?? $this->birthday,
            $expiredDate ?? $this->expiredDate,
            $workPrefecture ?? $this->workPrefecture,
            $workZipcode ?? $this->workZipcode
        );
    }
}