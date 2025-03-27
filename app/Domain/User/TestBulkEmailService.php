<?php

namespace App\Domain\Email;

use App\Domain\User\User;
use App\Domain\User\UserColumnList;
use App\Domain\Application\ApplicationColumnList;
use App\Domain\Screening\ScreeningColumnList;
use App\Domain\User\Prefecture;
use App\Domain\User\Gender;
use App\Domain\Application\ApplicationRepository;
use Illuminate\Support\Collection;

class BulkEmailService
{
    private ApplicationRepository $applicationRepository;
    private EmailSender $emailSender;

    public function __construct(
        ApplicationRepository $applicationRepository,
        EmailSender $emailSender
    ) {
        $this->applicationRepository = $applicationRepository;
        $this->emailSender = $emailSender;
    }

    public function sendBulkBatchEmail(
        Collection $users,
        UserColumnList $userColumns,
        ApplicationColumnList $applicationColumns,
        ScreeningColumnList $screeningColumns,
        string $templateName,
        Collection $totalUserIds
    ): void {
        $replacementTemplateDataList = $users->mapWithKeys(function (User $user) use (
            $userColumns,
            $applicationColumns,
            $screeningColumns
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

            // スクリーニング情報の設定
            if (!$screeningColumns->isEmpty()) {
                $templateData = array_merge(
                    $templateData,
                    $this->getScreeningTemplateData($user->getId(), $screeningColumns)
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

        $this->emailSender->sendBulkEmail($users, $totalUserIds, $templateName, $replacementTemplateDataList);
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
}
