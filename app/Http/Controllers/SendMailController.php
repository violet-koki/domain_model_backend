<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SendMailController extends Controller
{
    /**
     * メール一括送信
     *
     * @param  SendBulkEmailRequest $request
     * @return Response
     */
    public function sendBulkEmail()
    {
        dd('test');
        // $this->mailTemplateService->sendBulkEmail($request->validated());
        // return response('', 204);
    }
}
