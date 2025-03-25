<?php

namespace App\Domain\Email;

use App\Domain\User\User;
use App\Domain\User\UserColumnList;
use App\Domain\Application\ApplicationColumnList;
use App\Domain\Screening\ScreeningColumnList;
use Illuminate\Support\Collection;

class SESSystem
{
    public function sendBulkBatchEmail(
        Collection $users,
        UserColumnList $userColumns,
        ApplicationColumnList $applicationColumns,
        string $templateName,
        Collection $totalUserIds
    ): void {
        $replacementTemplateDataList = $users->mapWithKeys(function (User $user) use (
            $userColumns,
            $applicationColumns,
        ) {
            $templateData = [];

            // ユーザー情報の設定
            if (!$userColumns->isEmpty()) {
                $templateData = array_merge($templateData, $this->getUserTemplateData($user, $userColumns));
            }

            // アプリケーション情報の設定
            if (!$applicationColumns->isEmpty()) {
                $templateData = array_merge(
                    $templateData,
                    $this->getApplicationTemplateData($user->getId(), $applicationColumns)
                );
            }

            // 空の値をデフォルト値に置き換え
            foreach ($templateData as &$value) {
                if (!$value) {
                    $value = ' - ';
                }
            }

            return [$user->getId() => $templateData];
        })->toArray();

        $this->sendBulkEmail($users, $totalUserIds, $templateName, $replacementTemplateDataList);
    }

    private function getUserTemplateData(User $user, UserColumnList $columns): array
    {
        $templateData = [];

        // 住所処理
        if ($columns->hasAddress()) {
            $templateData['address'] = $user->getFormattedAddress();
        }

        // 勤務先住所処理
        if ($columns->hasWorkAddress()) {
            $templateData['work_address'] = $user->getFormattedWorkAddress();
        }

        // 各種日付フォーマット処理
        if ($columns->hasBirthday()) {
            $templateData['birthday'] = $user->getFormattedBirth();
        }

        if ($columns->hasExpiredDate()) {
            $templateData['expired_date'] = $user->getFormattedExpiredDate();
        }

        // 勤務先都道府県と郵便番号
        if ($columns->hasWorkPrefecture()) {
            $templateData['work_prefecture'] = $user->getFormattedWorkPrefecture();
        }

        if ($columns->hasWorkZipcode()) {
            $templateData['work_zipcode'] = $user->getFormattedWorkZipCode();
        }

        // 性別情報
        if ($columns->hasGender()) {
            $templateData['gender'] = $user->getFormattedGender();
        }

        // その他の標準フィールド
        foreach ($columns->getStandardColumns() as $column) {
            $templateData[$column] = $user->getProperty($column);
        }

        return $templateData;
    }

    private function getApplicationTemplateData(int $userId, ApplicationColumnList $columns): array
    {
        $templateData = [];
        $application = $this->applicationRepository->fetchApplication(['user_id' => $userId]);

        // 合格試験番号
        if ($columns->hasPassedExamineNumber()) {
            $passedApplication = $this->applicationRepository->fetchLatestPassedApplication($userId);
            $templateData['passed_examine_number'] =
                $passedApplication && $passedApplication->isPassFlagEnabled() ?
                $passedApplication->getExamineNumber() :
                null;
        }

        // 出席番号
        if ($columns->hasAttendanceNumber()) {
            $attendanceApplication = $this->applicationRepository->fetchLatestCompletedApplication($userId);
            $templateData['attendance_number'] =
                $attendanceApplication && $attendanceApplication->isAttendanceFlagEnabled() ?
                $attendanceApplication->getAttendanceNumber() :
                null;
        }

        // その他の標準フィールド
        foreach ($columns->getStandardColumns() as $column) {
            $templateData[$column] = $application ? $application->getProperty($column) : null;
        }

        return $templateData;
    }

    private function getScreeningTemplateData(int $userId, ScreeningColumnList $columns): array
    {
        $templateData = [];
        $application = $this->applicationRepository->fetchApplication(['user_id' => $userId]);

        if ($columns->hasExpAssoc() && $application && $application->getScreening()) {
            $templateData['exp_assoc'] = $application->getScreening()->getExpAssoc()->description();
        }

        return $templateData;
    }

    /**
     * メール一括送信 共通部分
     *
     * @param Collection $users
     * @param Collection $totalUserIds
     * @param string $templateName
     * @param array $replacementTemplateDataList
     * @param array $defaultTemplateData
     * @return void
     */
    private function sendBulkEmail(
        Collection $users,
        Collection $totalUserIds,
        string $templateName,
        array $replacementTemplateDataList = [],
        array $defaultTemplateData = []
    ): void {
        // bulkEmailEntriesの形式に加工
        $bulkEmailEntries = $users->map(function ($user) use ($replacementTemplateDataList) {
            return [
                'Destination' => [
                    'ToAddresses' => [$user->mail],
                    'CcAddresses' => [],
                    'BccAddresses' => [config('mail.bcc.address')]
                ],
                'ReplacementEmailContent' => [
                    'ReplacementTemplate' => [
                        'ReplacementTemplateData' => $this->getTemplateDataJson($replacementTemplateDataList[$user->id] ?? []),
                    ]
                ]
            ];
        })->toArray();
        $userIds = $users->pluck('id');
        $leftOverUserIds = $totalUserIds->filter(function ($id) use ($userIds) {
            return !($id <= $userIds->max() || $userIds->contains($id));
        });

        try {
            $this->sesClient->sendBulkEmail(
                [
                    'FromEmailAddress' => config('mail.from.name') . '<' . config('mail.from.address') . '>',
                    'DefaultContent' => [
                        'Template' => [
                            'TemplateName' => $templateName,
                            'TemplateData' => $this->getTemplateDataJson($defaultTemplateData),
                        ]
                    ],
                    'BulkEmailEntries' => $bulkEmailEntries
                ]
            );
        } catch (SesV2Exception $e) {
            Log::error('SES sendbulk error', [
                'ses error' => $e->getMessage(),
                'file' => $e->getFile(),
                'Line' => $e->getLine(),
                'Trace' => $e->getTraceAsString(),
            ]);
            // 送信に失敗したユーザ、未送信のユーザリストログ
            Log::info('ses', [
                'message' => 'SES bulk send users failed to send and users not sending',
                'failed_user_ids' => $userIds,
                'unsent_user_ids' => $leftOverUserIds,
                'action' => 'SendBulkEmail'
            ]);
            throw new SimpleEmailServiceException();
        }
    }
}
