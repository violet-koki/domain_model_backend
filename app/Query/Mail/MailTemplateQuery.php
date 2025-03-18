<?php

namespace App\Query\MailTemplate;

use App\Models\MailTemplate;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Consts\TemplateType;

class MailTemplateQuery
{
    /**
     * メールテンプレート取得
     *
     * @param array $searchValues
     * @return Collection
     */
    public function getMailTemplatesByTemplateType(array $searchValues): Collection
    {
        $result = MailTemplate::whereIn('template_type', $searchValues)
            ->get();
        return $result;
    }

    /**
     * メールテンプレート情報取得
     *
     * @param array $searchParams
     * @return MailTemplate
     */
    public function fetchOne(array $searchParams): MailTemplate
    {
        $result = $this->findQuery($searchParams)->first();
        if (!$result) {
            throw new NotFoundHttpException();
        }
        return $result;
    }

    /**
     * メールテンプレートクエリ生成
     *
     * @param array $searchParams
     * @return Builder
     */
    public function findQuery(array $searchParams): Builder
    {
        return MailTemplate::query()->where($searchParams);
    }

    /**
     * システムメールのSESメールテンプレート名を取得
     *
     * @param string $templateName
     * @throws NotFoundHttpException
     * @return string
     */
    public function fetchSystemSESTemplateName(string $templateName): string
    {
        /** @var MailTemplate|null $result */
        $result = $this->findQuery([
            'template_name' => $templateName,
            'template_type' => TemplateType::System->value,
        ])->first();
        if (!$result) {
            throw new NotFoundHttpException();
        }
        return $result->ses_template_name;
    }
}
