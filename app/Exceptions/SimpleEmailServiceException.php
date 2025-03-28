<?php

namespace App\Exceptions;

use App\Exceptions\BaseException;
use Illuminate\Http\Response;
use Throwable;

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
     * 
     * @param string|null $customMessage カスタムエラーメッセージ
     * @param Throwable|null $previous 元の例外
     */
    public function __construct(?string $customMessage = null, ?Throwable $previous = null)
    {
        $message = $customMessage ?: $this->errorMessage;
        parent::__construct($message, 0, $previous);
    }
}
