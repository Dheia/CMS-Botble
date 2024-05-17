<?php

namespace Botble\Member\Forms;

use Botble\Base\Forms\FieldOptions\TextareaFieldOption;
use Botble\Collection\Forms\SubjectForm as BaseSubjectForm;
use Botble\Collection\Models\Subject;
use Botble\Member\Forms\Fields\CustomEditorField;
use Botble\Member\Forms\Fields\CustomImageField;
use Botble\Member\Http\Requests\SubjectRequest;

class SubjectForm extends BaseSubjectForm
{
    public function setup(): void
    {
        parent::setup();

        $this
            ->model(Subject::class)
            ->setFormOption('template', 'plugins/member::forms.base')
            ->hasFiles()
            ->setValidatorClass(SubjectRequest::class)
            ->setBreakFieldPoint('taxon[]')
            ->addCustomField('customImage', CustomImageField::class)
            ->remove('status')
            ->remove('is_featured')
            ->remove('content')
            ->addAfter(
                'description',
                'content',
                CustomEditorField::class,
                TextareaFieldOption::make()->label(trans('core/base::forms.content'))->rows(4)->toArray()
            );
    }
}
