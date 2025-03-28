<?php

namespace App\Exceptions;

use Illuminate\Http\Response;

/**
 * Exception基底クラス
 */
abstract class BaseException extends \Exception
{
    /** @var int HTTPステータスコード */
    protected int $statusCode;

    /** @var string エラーコード */
    protected string $errorCode;

    /** @var string エラーメッセージ */
    protected string $errorMessage;

    /**
     * Render an exception into an HTTP response.
     *
     * @return Response
     */
    public function render(): Response
    {
        return response([
            'code' => $this->errorCode,
            'message' => $this->errorMessage,
        ], $this->statusCode);
    }
}