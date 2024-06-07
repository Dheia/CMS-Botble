<?php

namespace Botble\Collection\Http\Requests\Settings;

use Botble\Base\Rules\OnOffRule;
use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class CollectionSettingRequest extends Request
{
    public function rules(): array
    {
        return [
            'collection_subject_schema_enabled' => new OnOffRule(),
            'collection_subject_schema_type' => [
                'nullable',
                'string',
                Rule::in(['NewsArticle', 'News', 'Article']),
            ],
        ];
    }
}
