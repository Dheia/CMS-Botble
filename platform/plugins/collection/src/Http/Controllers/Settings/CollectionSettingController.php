<?php

namespace Botble\Collection\Http\Controllers\Settings;

use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Collection\Forms\Settings\CollectionSettingForm;
use Botble\Collection\Http\Requests\Settings\CollectionSettingRequest;
use Botble\Setting\Http\Controllers\SettingController;

class CollectionSettingController extends SettingController
{
    public function edit()
    {
        $this->pageTitle(trans('plugins/collection::base.settings.title'));

        return CollectionSettingForm::create()->renderForm();
    }

    public function update(CollectionSettingRequest $request): BaseHttpResponse
    {
        return $this->performUpdate($request->validated());
    }
}
