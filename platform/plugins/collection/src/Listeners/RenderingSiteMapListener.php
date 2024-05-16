<?php

namespace Botble\Collection\Listeners;

use Botble\Collection\Models\Taxon;
use Botble\Collection\Models\Subject;
use Botble\Theme\Events\RenderingSiteMapEvent;
use Botble\Theme\Facades\SiteMapManager;
use Illuminate\Support\Arr;

class RenderingSiteMapListener
{
    public function handle(RenderingSiteMapEvent $event): void
    {
        if ($key = $event->key) {
            switch ($key) {
                case 'collection-taxons':
                    $taxons = Taxon::query()
                        ->with('slugable')
                        ->wherePublished()
                        ->select(['id', 'name', 'updated_at'])
                        ->orderByDesc('created_at')
                        ->get();

                    foreach ($taxons as $taxon) {
                        SiteMapManager::add($taxon->url, $taxon->updated_at, '0.8');
                    }

                    break;
            }

            if (preg_match('/^collection-subjects-((?:19|20|21|22)\d{2})-(0?[1-9]|1[012])$/', $key, $matches)) {
                if (($year = Arr::get($matches, 1)) && ($month = Arr::get($matches, 2))) {
                    $subjects = Subject::query()
                        ->wherePublished()
                        ->whereYear('created_at', $year)
                        ->whereMonth('created_at', $month)
                        ->latest('updated_at')
                        ->select(['id', 'name', 'updated_at'])
                        ->with(['slugable'])
                        ->get();

                    foreach ($subjects as $subject) {
                        if (! $subject->slugable) {
                            continue;
                        }

                        SiteMapManager::add($subject->url, $subject->updated_at, '0.8');
                    }
                }
            }

            return;
        }

        $subjects = Subject::query()
            ->selectRaw('YEAR(created_at) as created_year, MONTH(created_at) as created_month, MAX(created_at) as created_at')
            ->wherePublished()
            ->groupBy('created_year', 'created_month')
            ->orderByDesc('created_year')
            ->orderByDesc('created_month')
            ->get();

        if ($subjects->isNotEmpty()) {
            foreach ($subjects as $subject) {
                $key = sprintf('collection-subjects-%s-%s', $subject->created_year, str_pad($subject->created_month, 2, '0', STR_PAD_LEFT));
                SiteMapManager::addSitemap(SiteMapManager::route($key), $subject->created_at);
            }
        }

        $taxonLastUpdated = Taxon::query()
            ->wherePublished()
            ->latest('updated_at')
            ->value('updated_at');

        if ($taxonLastUpdated) {
            SiteMapManager::addSitemap(SiteMapManager::route('collection-taxons'), $taxonLastUpdated);
        }
    }
}
