<?php

namespace Botble\Collection\Widgets\Fronts;

use Botble\Base\Forms\FieldOptions\NameFieldOption;
use Botble\Base\Forms\FieldOptions\NumberFieldOption;
use Botble\Base\Forms\Fields\NumberField;
use Botble\Base\Forms\Fields\TextField;
use Botble\Collection\Models\Taxon;
use Botble\Widget\AbstractWidget;
use Botble\Widget\Forms\WidgetForm;
use Illuminate\Support\Collection;

class Taxons extends AbstractWidget
{
    public function __construct()
    {
        parent::__construct([
            'name' => __('Collection Taxons'),
            'description' => __('Widget display collection taxons'),
            'number_display' => 10,
        ]);
    }

    protected function data(): array|Collection
    {
        if (! is_plugin_active('collection')) {
            return [];
        }

        $taxons = Taxon::query()
            ->wherePublished()
            ->with('slugable')
            ->take((int)$this->getConfig('number_display') ?: 10)
            ->get();

        return [
            'taxons' => $taxons,
        ];
    }

    protected function settingForm(): WidgetForm|string|null
    {
        if (! is_plugin_active('collection')) {
            return null;
        }

        return WidgetForm::createFromArray($this->getConfig())
            ->add('name', TextField::class, NameFieldOption::make()->toArray())
            ->add(
                'number_display',
                NumberField::class,
                NumberFieldOption::make()
                    ->label(__('Number taxons to display'))
                    ->toArray()
            );
    }
}
