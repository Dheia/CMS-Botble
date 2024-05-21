<?php

namespace Botble\Base\Forms\FieldOptions;

class WebsiteFieldOption extends TextFieldOption
{
    public static function make(): static
    {
        return parent::make()
            ->label(trans('core/base::forms.website'))
            ->placeholder(trans('core/base::forms.website_placeholder'))
            ->maxLength(250);
    }
}
