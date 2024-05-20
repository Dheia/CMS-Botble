<?php

namespace Botble\Base\Forms\FieldOptions;

class LinkFieldOption extends TextFieldOption
{
    public static function make(): static
    {
        return parent::make()
            ->label(trans('core/base::forms.link'))
            ->placeholder(trans('core/base::forms.link_placeholder'))
            ->maxLength(250);
    }
}
