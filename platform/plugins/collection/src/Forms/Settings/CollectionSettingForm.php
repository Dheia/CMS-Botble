<?php

namespace Botble\Collection\Forms\Settings;

use Botble\Collection\Http\Requests\Settings\CollectionSettingRequest;
use Botble\Setting\Forms\SettingForm;

class CollectionSettingForm extends SettingForm
{
    public function setup(): void
    {
        parent::setup();

        $this
            ->setSectionTitle(trans('plugins/collection::base.settings.title'))
            ->setSectionDescription(trans('plugins/collection::base.settings.description'))
            ->setValidatorClass(CollectionSettingRequest::class)
            ->add('collection_setting', 'html', [
                'html' => view('plugins/collection::partials.collection-subject-schema-fields'),
                'wrapper' => [
                    'class' => 'mb-0',
                ],
            ]);
    }
}
