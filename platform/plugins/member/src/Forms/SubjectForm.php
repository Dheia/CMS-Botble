<?php

namespace Botble\Member\Forms;

use Botble\Base\Forms\FieldOptions\TagFieldOption;
use Botble\Base\Forms\FieldOptions\TextareaFieldOption;
use Botble\Base\Forms\Fields\TagField;
use Botble\Collection\Forms\SubjectForm as BaseSubjectForm;
use Botble\Collection\Models\Subject;
use Botble\Collection\Models\Tag;
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
            ->setBreakFieldPoint('categories[]')
            ->addCustomField('customImage', CustomImageField::class)
            ->remove('status')
            ->remove('is_featured')
            ->remove('content')
            ->addAfter(
                'description',
                'content',
                CustomEditorField::class,
                TextareaFieldOption::make()->label(trans('core/base::forms.content'))->rows(4)->toArray()
            )
            ->modify(
                'tag',
                TagField::class,
                TagFieldOption::make()
                    ->label(trans('plugins/collection::subjects.form.tags'))
                    ->when($this->getModel()->id, function (TagFieldOption $fieldOption) {
                        return $fieldOption
                            ->value(
                                $this->getModel()
                                    ->tags()
                                    ->select('name')
                                    ->get()
                                    ->map(fn (Tag $item) => $item->name)
                                    ->implode(',')
                            );
                    })
                    ->placeholder(trans('plugins/collection::base.write_some_tags'))
                    ->ajaxUrl(route('public.member.tags.all'))
                    ->toArray(),
                true
            );
    }
}
