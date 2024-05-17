<?php

namespace Botble\Collection\Forms\Fields;

use Botble\Base\Forms\FormField;

/**
 * @deprecated
 */
class TaxonMultiField extends FormField
{
    protected function getTemplate(): string
    {
        return 'core/base::forms.fields.tree-taxons';
    }
}
