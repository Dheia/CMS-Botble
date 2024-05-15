<?php

namespace Botble\Collection\Services;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Supports\Helper;
use Botble\Collection\Models\Taxon;
use Botble\Collection\Models\Subject;
use Botble\Collection\Repositories\Interfaces\SubjectInterface;
use Botble\Media\Facades\RvMedia;
use Botble\SeoHelper\Facades\SeoHelper;
use Botble\SeoHelper\SeoOpenGraph;
use Botble\Slug\Models\Slug;
use Botble\Theme\Facades\AdminBar;
use Botble\Theme\Facades\Theme;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class CollectionService
{
    public function handleFrontRoutes(Slug|array $slug): Slug|array|Builder
    {
        if (! $slug instanceof Slug) {
            return $slug;
        }

        $condition = [
            'id' => $slug->reference_id,
            'status' => BaseStatusEnum::PUBLISHED,
        ];

        if (Auth::guard()->check() && request()->input('preview')) {
            Arr::forget($condition, 'status');
        }

        switch ($slug->reference_type) {
            case Subject::class:
                /**
                 * @var Subject $subject
                 */
                $subject = Subject::query()
                    ->where($condition)
                    ->with(['taxon', 'slugable', 'taxon.slugable'])
                    ->firstOrFail();

                Helper::handleViewCount($subject, 'viewed_subject');

                SeoHelper::setTitle($subject->name)
                    ->setDescription($subject->description);

                $meta = new SeoOpenGraph();
                if ($subject->image) {
                    $meta->setImage(RvMedia::getImageUrl($subject->image));
                }
                $meta->setDescription($subject->description);
                $meta->setUrl($subject->url);
                $meta->setTitle($subject->name);
                $meta->setType('article');

                SeoHelper::setSeoOpenGraph($meta);

                SeoHelper::meta()->setUrl($subject->url);

                if (function_exists('admin_bar')) {
                    AdminBar::registerLink(
                        trans('plugins/collection::subjects.edit_this_subject'),
                        route('subjects.edit', $subject->getKey()),
                        null,
                        'subjects.edit'
                    );
                }

                if (function_exists('shortcode')) {
                    shortcode()->getCompiler()->setEditLink(route('subjects.edit', $subject->id), 'subjects.edit');
                }

                $taxon = $subject->taxon->sortByDesc('id')->first();
                if ($taxon) {
                    if ($taxon->parents->isNotEmpty()) {
                        foreach ($taxon->parents as $parentTaxon) {
                            Theme::breadcrumb()->add($parentTaxon->name, $parentTaxon->url);
                        }
                    }

                    Theme::breadcrumb()->add($taxon->name, $taxon->url);
                }

                Theme::breadcrumb()->add($subject->name, $subject->url);

                do_action(BASE_ACTION_PUBLIC_RENDER_SINGLE, SUBJECT_MODULE_SCREEN_NAME, $subject);

                return [
                    'view' => 'subject',
                    'default_view' => 'plugins/collection::themes.subject',
                    'data' => compact('subject'),
                    'slug' => $subject->slug,
                ];
            case Taxon::class:
                $taxon = Taxon::query()
                    ->where($condition)
                    ->with(['slugable'])
                    ->firstOrFail();

                SeoHelper::setTitle($taxon->name)
                    ->setDescription($taxon->description);

                $meta = new SeoOpenGraph();
                if ($taxon->image) {
                    $meta->setImage(RvMedia::getImageUrl($taxon->image));
                }
                $meta->setDescription($taxon->description);
                $meta->setUrl($taxon->url);
                $meta->setTitle($taxon->name);
                $meta->setType('article');

                SeoHelper::setSeoOpenGraph($meta);

                SeoHelper::meta()->setUrl($taxon->url);

                if (function_exists('admin_bar')) {
                    AdminBar::registerLink(
                        trans('plugins/collection::taxon.edit_this_taxon'),
                        route('taxon.edit', $taxon->getKey()),
                        null,
                        'taxon.edit'
                    );
                }

                $allRelatedTaxonIds = array_merge([$taxon->getKey()], $taxon->activeChildren->pluck('id')->all());

                $subjects = app(SubjectInterface::class)
                    ->getByTaxon($allRelatedTaxonIds, (int)theme_option('number_of_subjects_in_a_taxon', 12));

                if ($taxon->parents->isNotEmpty()) {
                    foreach ($taxon->parents->reverse() as $parentTaxon) {
                        Theme::breadcrumb()->add($parentTaxon->name, $parentTaxon->url);
                    }
                }

                Theme::breadcrumb()->add($taxon->name, $taxon->url);

                do_action(BASE_ACTION_PUBLIC_RENDER_SINGLE, TAXON_MODULE_SCREEN_NAME, $taxon);

                return [
                    'view' => 'taxon',
                    'default_view' => 'plugins/collection::themes.taxon',
                    'data' => compact('taxon', 'subjects'),
                    'slug' => $taxon->slug,
                ];
        }

        return $slug;
    }
}
