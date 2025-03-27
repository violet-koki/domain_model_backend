<?php

namespace App\Http\Controllers;

use App\Services\Mail\MailService;
use Illuminate\Http\Request;
use App\Http\Requests\BulkEmailRequest;
use Illuminate\Http\Response;

class SendMailController extends Controller
{
    private $mailService;
    public function __construct(MailService $mailService)
    {
        $this->mailService = $mailService;
    }
    /**
     * メール一括送信
     *
     * @param  BulkEmailRequest $request
     * @return Response
     */
    public function sendBulkEmail(BulkEmailRequest $request)
    {
        $this->mailService->sendBulkEmail($request->validated());
        return response('', 204);
    }
}