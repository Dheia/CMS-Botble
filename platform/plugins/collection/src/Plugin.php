<?php

namespace Botble\Collection;

use Botble\Collection\Models\Taxon;
use Botble\Dashboard\Models\DashboardWidget;
use Botble\Menu\Models\MenuNode;
use Botble\PluginManagement\Abstracts\PluginOperationAbstract;
use Botble\Setting\Facades\Setting;
use Botble\Widget\Models\Widget;
use Illuminate\Support\Facades\Schema;

class Plugin extends PluginOperationAbstract
{
    public static function remove(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('subject_taxon');
        Schema::dropIfExists('subjects');
        Schema::dropIfExists('taxon');
        Schema::dropIfExists('subjects_translations');
        Schema::dropIfExists('taxon_translations');

        Widget::query()
            ->where('widget_id', 'widget_subjects_recent')
            ->each(fn (DashboardWidget $dashboardWidget) => $dashboardWidget->delete());

        MenuNode::query()
            ->whereIn('reference_type', [Taxon::class])
            ->each(fn (MenuNode $menuNode) => $menuNode->delete());

        Setting::delete([
            'collection_subject_schema_enabled',
            'collection_subject_schema_type',
        ]);
    }
}
