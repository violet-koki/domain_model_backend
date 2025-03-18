<?php

namespace App\Services\Mail;

use App\Query\MailTemplate\MailTemplateQuery;
use App\Consts\TemplateType;

class MailService
{
    private $mailTemplateQuery;
    public function __construct(
        MailTemplateQuery $mailTemplateQuery
    )
    {
        $this->mailTemplateQuery = $mailTemplateQuery;
    }
    public function sendBulkEmail()
    {

    }
    /**
     * メール一括送信
     *
     * @param  array $request
     * @return void
     */
    public function formerSendBulkEmail(array $request)
    {
        $mailTemplate = $this->mailTemplateQuery->fetchOne([
            'id' => $request['id'],
            'template_type' => TemplateType::BatchSending,
        ]);
        $variableNameList = $mailTemplate->MailTemplateVariables()->pluck('variable_name')->toArray();
        $userColumnNameList = ['certification_number', 'name', 'name_kana', 'gender', 'birthday', 'doctor_number', 'doctor_registration_date', 'work_name', 'work_section', 'work_zipcode', 'work_prefecture', 'work_address', 'work_phone', 'mail', 'address', 'expired_date'];
        $applicationColumnNameList = ['receipt_number', 'examine_number', 'passed_examine_number', 'attendance_number'];
        $screeningColumnNameList = ['exp_assoc'];
        // テンプレート変数のうち、users,applications,screeningsテーブルに含まれるカラムを配列で取得する
        $userColumns = array_intersect($userColumnNameList, $variableNameList);
        $applicationColumns = array_intersect($applicationColumnNameList, $variableNameList);
        $screeningColumn = array_intersect($screeningColumnNameList, $variableNameList);

        //getDestinationにより、送付先の取得、それからuserテーブルから取得すべきカラムを$userColumnsにまとめる。
        $users = $this->getDestination($request, $userColumns)->sortBy('id')->values();
        $totalUserIds = $users->pluck('id');
        // SesV2Clientを作成、AWSリソースを操作
        $mail = $this->createMailSystem();
        // SESの仕様の関係で、メール送付先は14件ごと渡す
        $targets = $users->chunk(14);
        foreach ($targets as $chunk) {
            $mail->sendBulkBatchSendingEmail($chunk->values(), $userColumns, $applicationColumns, $screeningColumn, $mailTemplate->ses_template_name, $totalUserIds);
            sleep(3);
        }
        $userIds = $totalUserIds->toArray();
        // メール送信対象ユーザIDをログ出力
        Log::info('ses', [
            'message' => 'SES bulk send target users',
            'user_ids' => $userIds,
            'action' => 'SendBulkEmail'
        ]);
    }
}