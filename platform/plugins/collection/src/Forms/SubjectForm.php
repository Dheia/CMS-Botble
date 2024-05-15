<?php

namespace Botble\Collection\Forms;

use Botble\Base\Forms\FieldOptions\ContentFieldOption;
use Botble\Base\Forms\FieldOptions\DescriptionFieldOption;
use Botble\Base\Forms\FieldOptions\IsFeaturedFieldOption;
use Botble\Base\Forms\FieldOptions\NameFieldOption;
use Botble\Base\Forms\FieldOptions\RadioFieldOption;
use Botble\Base\Forms\FieldOptions\SelectFieldOption;
use Botble\Base\Forms\FieldOptions\StatusFieldOption;
use Botble\Base\Forms\FieldOptions\TagFieldOption;
use Botble\Base\Forms\Fields\EditorField;
use Botble\Base\Forms\Fields\MediaImageField;
use Botble\Base\Forms\Fields\OnOffField;
use Botble\Base\Forms\Fields\RadioField;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Forms\Fields\TagField;
use Botble\Base\Forms\Fields\TextareaField;
use Botble\Base\Forms\Fields\TextField;
use Botble\Base\Forms\Fields\TreeCategoryField;
use Botble\Base\Forms\FormAbstract;
use Botble\Collection\Http\Requests\SubjectRequest;
use Botble\Collection\Models\Category;
use Botble\Collection\Models\Subject;
use Botble\Collection\Models\Tag;

class SubjectForm extends FormAbstract
{
    public function setup(): void
    {
        $this
            ->model(Subject::class)
            ->setValidatorClass(SubjectRequest::class)
            ->hasTabs()
            ->add('name', TextField::class, NameFieldOption::make()->required()->toArray())
            ->add('description', TextareaField::class, DescriptionFieldOption::make()->toArray())
            ->add(
                'is_featured',
                OnOffField::class,
                IsFeaturedFieldOption::make()
                    ->toArray()
            )
            ->add('content', EditorField::class, ContentFieldOption::make()->allowedShortcodes()->toArray())
            ->add('status', SelectField::class, StatusFieldOption::make()->toArray())
            ->when(get_subject_formats(true), function (SubjectForm $form, array $subjectFormats) {
                if (count($subjectFormats) > 1) {
                    $choices = [];

                    foreach ($subjectFormats as $subjectFormat) {
                        $choices[$subjectFormat[0]] = $subjectFormat[1];
                    }

                    $form
                        ->add(
                            'format_type',
                            RadioField::class,
                            RadioFieldOption::make()
                                ->label(trans('plugins/collection::subjects.form.format_type'))
                                ->choices($choices)
                                ->toArray()
                        );
                }
            })
            ->add(
                'categories[]',
                TreeCategoryField::class,
                SelectFieldOption::make()
                    ->label(trans('plugins/collection::subjects.form.categories'))
                    ->choices(get_categories_with_children())
                    ->when($this->getModel()->id, function (SelectFieldOption $fieldOption) {
                        return $fieldOption->selected($this->getModel()->categories()->pluck('category_id')->all());
                    })
                    ->when(! $this->getModel()->id, function (SelectFieldOption $fieldOption) {
                        return $fieldOption
                            ->selected(Category::query()
                                ->where('is_default', 1)
                                ->pluck('id')
                                ->all());
                    })
                    ->toArray()
            )
            ->add('image', MediaImageField::class)
            ->add(
                'tag',
                TagField::class,
                TagFieldOption::make()
                    ->label(trans('plugins/collection::subjects.form.tags'))
                    ->when($this->getModel()->id, function (TagFieldOption $fieldOption) {
                        return $fieldOption
                            ->selected(
                                $this->getModel()
                                ->tags()
                                ->select('name')
                                ->get()
                                ->map(fn (Tag $item) => $item->name)
                                ->implode(',')
                            );
                    })
                    ->placeholder(trans('plugins/collection::base.write_some_tags'))
                    ->ajaxUrl(route('tags.all'))
                    ->toArray()
            )
            ->setBreakFieldPoint('status');
    }
}
