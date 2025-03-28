<?php

namespace App\Domain\Email;

use App\Domain\User\UserColumnList;
use App\Domain\Application\ApplicationColumnList;
use Illuminate\Support\Collection;
use Aws\SesV2\SesV2Client;
use Aws\SesV2\Exception\SesV2Exception;
use App\Exceptions\SimpleEmailServiceException;
use App\Models\User as UserModel;
use App\Domain\User\User;
use App\Domain\Application\Application;
use Illuminate\Support\Facades\Log;



class SESSystem
{
    private SesV2Client $sesClient;

    /**
     * コンストラクタ
     */
    public function __construct()
    {
        $this->sesClient = self::getClient();
    }
    public function sendBulkBatchEmail(
        Collection $users,
        UserColumnList $userColumns,
        ApplicationColumnList $applicationColumns,
        string $templateName,
        Collection $totalUserIds
    ): void {
        $replacementTemplateDataList = $users->mapWithKeys(function (UserModel $userModel) use (
            $userColumns,
            $applicationColumns,
        ) {
            $templateData = [];
            $user = User::fromModel($userModel);

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
    /**
     * SesV2Clientを取得
     *
     * @return SesV2Client
     */
    private static function getClient(): SesV2Client
    {
        try {
            return new SesV2Client([
                'version' => config('services.ses.version'),
                'region' => config('services.ses.region'),
                'endpoint' => config('services.ses.endpoint'),
                'retries' => config('services.retries'),
                'http' => [
                    'connect_timeout' => config('services.http.connect_timeout'),
                ]
            ]);
        } catch (SesV2Exception $e) {
            throw new SimpleEmailServiceException("メール送信に失敗しました: " . $e->getMessage(), $e);
        }
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


        return $templateData;
    }


    /**
     * 申請情報からテンプレートデータを取得
     *
     * @param int $userId ユーザーID
     * @param ApplicationColumnList $columns 列設定
     * @return array
     */
    private function getApplicationTemplateData(int $userId, ApplicationColumnList $columns): array
    {
        $templateData = [];

        // 申込の取得
        $application = Application::fetchApplication(['user_id' => $userId]);

        // 合格試験番号
        if ($columns->hasPassedExamineNumber()) {
            $passedApplication = Application::fetchLatestPassedApplication($userId);
            $templateData['passed_examine_number'] =
                $passedApplication && $passedApplication->isPassFlagEnabled() ?
                $passedApplication->getExamineNumber() :
                null;
        }

        // 出席番号
        if ($columns->hasAttendanceNumber()) {
            $attendanceApplication = Application::fetchLatestCompletedApplication($userId);
            $templateData['attendance_number'] =
                $attendanceApplication && $attendanceApplication->isAttendanceFlagEnabled() ?
                $attendanceApplication->getAttendanceNumber() :
                null;
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
                    'CcAddresses' => ['test1@example.com'],
                    'BccAddresses' => ['test2@example.com']
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
        // dd($templateName, $defaultTemplateData, $bulkEmailEntries);

        try {
            $this->sesClient->sendBulkEmail(
                [
                    'FromEmailAddress' => config('mail.from.name') . '<' . config('mail.from.address') . '>',
                    'DefaultContent' => [
                        'Template' => [
                            'TemplateName' => $templateName,
                            // 'TemplateData' => $this->getTemplateDataJson($defaultTemplateData),
                            'TemplateData' => app()->isLocal() ? "[]" : "{}"
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
            throw new SimpleEmailServiceException("メール送信に失敗しました: " . $e->getMessage(), $e);
        }
    }

    /**
     * テンプレート置換データのJSONを取得
     *
     * @param array $templateData
     * @return string
     */
    private function getTemplateDataJson(array $templateData): string
    {
        if (app()->isLocal()) {
            $tmpTemplateData = $templateData;
            $templateData = [];
            foreach ($tmpTemplateData as $key => $val) {
                $templateData[] = ['Name' => $key, 'Value' => $val];
            }
            $templateDataJson = json_encode($templateData);
        } else {
            $templateDataJson = json_encode($templateData, JSON_FORCE_OBJECT);
        }
        return $templateDataJson;
    }
}