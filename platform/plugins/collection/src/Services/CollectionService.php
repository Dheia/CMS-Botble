<?php

namespace Botble\Collection\Services;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Supports\Helper;
use Botble\Collection\Models\Category;
use Botble\Collection\Models\Subject;
use Botble\Collection\Models\Tag;
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
                    ->with(['categories', 'tags', 'slugable', 'categories.slugable', 'tags.slugable'])
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

                $category = $subject->categories->sortByDesc('id')->first();
                if ($category) {
                    if ($category->parents->isNotEmpty()) {
                        foreach ($category->parents as $parentCategory) {
                            Theme::breadcrumb()->add($parentCategory->name, $parentCategory->url);
                        }
                    }

                    Theme::breadcrumb()->add($category->name, $category->url);
                }

                Theme::breadcrumb()->add($subject->name, $subject->url);

                do_action(BASE_ACTION_PUBLIC_RENDER_SINGLE, POST_MODULE_SCREEN_NAME, $subject);

                return [
                    'view' => 'subject',
                    'default_view' => 'plugins/collection::themes.subject',
                    'data' => compact('subject'),
                    'slug' => $subject->slug,
                ];
            case Category::class:
                $category = Category::query()
                    ->where($condition)
                    ->with(['slugable'])
                    ->firstOrFail();

                SeoHelper::setTitle($category->name)
                    ->setDescription($category->description);

                $meta = new SeoOpenGraph();
                if ($category->image) {
                    $meta->setImage(RvMedia::getImageUrl($category->image));
                }
                $meta->setDescription($category->description);
                $meta->setUrl($category->url);
                $meta->setTitle($category->name);
                $meta->setType('article');

                SeoHelper::setSeoOpenGraph($meta);

                SeoHelper::meta()->setUrl($category->url);

                if (function_exists('admin_bar')) {
                    AdminBar::registerLink(
                        trans('plugins/collection::categories.edit_this_category'),
                        route('categories.edit', $category->getKey()),
                        null,
                        'categories.edit'
                    );
                }

                $allRelatedCategoryIds = array_merge([$category->getKey()], $category->activeChildren->pluck('id')->all());

                $subjects = app(SubjectInterface::class)
                    ->getByCategory($allRelatedCategoryIds, (int)theme_option('number_of_subjects_in_a_category', 12));

                if ($category->parents->isNotEmpty()) {
                    foreach ($category->parents->reverse() as $parentCategory) {
                        Theme::breadcrumb()->add($parentCategory->name, $parentCategory->url);
                    }
                }

                Theme::breadcrumb()->add($category->name, $category->url);

                do_action(BASE_ACTION_PUBLIC_RENDER_SINGLE, CATEGORY_MODULE_SCREEN_NAME, $category);

                return [
                    'view' => 'category',
                    'default_view' => 'plugins/collection::themes.category',
                    'data' => compact('category', 'subjects'),
                    'slug' => $category->slug,
                ];
            case Tag::class:
                $tag = Tag::query()
                    ->where($condition)
                    ->with(['slugable'])
                    ->firstOrFail();

                SeoHelper::setTitle($tag->name)
                    ->setDescription($tag->description);

                $meta = new SeoOpenGraph();
                $meta->setDescription($tag->description);
                $meta->setUrl($tag->url);
                $meta->setTitle($tag->name);
                $meta->setType('article');

                SeoHelper::setSeoOpenGraph($meta);

                SeoHelper::meta()->setUrl($tag->url);

                if (function_exists('admin_bar')) {
                    AdminBar::registerLink(
                        trans('plugins/collection::tags.edit_this_tag'),
                        route('tags.edit', $tag->getKey()),
                        null,
                        'tags.edit'
                    );
                }

                $subjects = get_subjects_by_tag($tag->getKey(), (int)theme_option('number_of_subjects_in_a_tag', 12));

                Theme::breadcrumb()->add($tag->name, $tag->url);

                do_action(BASE_ACTION_PUBLIC_RENDER_SINGLE, TAG_MODULE_SCREEN_NAME, $tag);

                return [
                    'view' => 'tag',
                    'default_view' => 'plugins/collection::themes.tag',
                    'data' => compact('tag', 'subjects'),
                    'slug' => $tag->slug,
                ];
        }

        return $slug;
    }
}
