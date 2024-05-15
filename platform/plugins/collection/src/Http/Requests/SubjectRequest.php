<?php

namespace Botble\Collection\Http\Requests;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Rules\MediaImageRule;
use Botble\Collection\Supports\SubjectFormat;
use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class SubjectRequest extends Request
{
    public function rules(): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:250'],
            'description' => ['nullable', 'string', 'max:400'],
            'content' => ['nullable', 'string', 'max:300000'],
            'taxon' => ['sometimes', 'array'],
            'taxon.*' => ['sometimes', 'exists:taxon,id'],
            'status' => Rule::in(BaseStatusEnum::values()),
            'is_featured' => ['sometimes', 'boolean'],
            'image' => ['nullable', 'string', new MediaImageRule()],
        ];

        $subjectFormats = SubjectFormat::getSubjectFormats(true);

        if (count($subjectFormats) > 1) {
            $rules['format_type'] = Rule::in(array_keys($subjectFormats));
        }

        return $rules;
    }
}
