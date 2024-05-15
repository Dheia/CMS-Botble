<?php

namespace Botble\Member\Http\Requests;

use Botble\Collection\Http\Requests\SubjectRequest as BaseSubjectRequest;
use Botble\Media\Facades\RvMedia;

class SubjectRequest extends BaseSubjectRequest
{
    public function rules(): array
    {
        $rules = parent::rules();

        if ($this->hasFile('image_input')) {
            $rules['image_input'] = RvMedia::imageValidationRule();

            unset($rules['image']);
        }

        return $rules;
    }
}
