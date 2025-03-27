<?php

namespace App\Query\User;

use Illuminate\Support\Collection;
use App\Models\User;

class UserQuery
{
    /**
     * 指定されたユーザIDのユーザ情報を取得する。
     *
     * @param array $targets
     * @param array $userColumns
     * @return Collection
     */
    public function fetchUsersByIds($targets, $userColumns): Collection
    {
        return User::whereIn('id', $targets)
            ->select(['id', 'mail']) // 必須カラム
            ->addSelect($userColumns) // 追加の要求されたカラム
            ->get();
    }

    public function fetchUsersByCertificationNumbers($targets, $userColumns): Collection
    {
        return User::whereIn('certification_number', $targets)
            ->select(['id', 'mail']) // 必須カラム
            ->addSelect($userColumns) // 追加の要求されたカラム
            ->get();
    }
}