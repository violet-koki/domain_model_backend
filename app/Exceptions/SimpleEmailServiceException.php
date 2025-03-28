<?php

namespace App\Exceptions;

use App\Exceptions\BaseException;
use Illuminate\Http\Response;

class SimpleEmailServiceException extends BaseException
{
    /** @var int HTTPステータスコード */
    protected int $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR;

    /** @var string エラーコード */
    protected string $errorCode = 'E50004';

    /** @var string エラーメッセージ */
    protected string $errorMessage = 'Simple Email Service Error';

    /**
     * コンストラクタ
     */
    public function __construct()
    {
        parent::__construct($this->errorMessage);
    }
}