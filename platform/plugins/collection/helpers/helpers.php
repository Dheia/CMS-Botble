<?php

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Facades\BaseHelper;
use Botble\Base\Models\BaseModel;
use Botble\Base\Supports\SortItemsWithChildrenHelper;
use Botble\Collection\Repositories\Interfaces\CategoryInterface;
use Botble\Collection\Repositories\Interfaces\SubjectInterface;
use Botble\Collection\Repositories\Interfaces\TagInterface;
use Botble\Collection\Supports\SubjectFormat;
use Botble\Page\Models\Page;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

if (! function_exists('get_featured_subjects')) {
    function get_featured_subjects(int $limit, array $with = []): Collection
    {
        return app(SubjectInterface::class)->getFeatured($limit, $with);
    }
}

if (! function_exists('get_latest_subjects')) {
    function get_latest_subjects(int $limit, array $excepts = [], array $with = []): Collection
    {
        return app(SubjectInterface::class)->getListSubjectNonInList($excepts, $limit, $with);
    }
}

if (! function_exists('get_related_subjects')) {
    function get_related_subjects(int|string $id, int $limit): Collection
    {
        return app(SubjectInterface::class)->getRelated($id, $limit);
    }
}

if (! function_exists('get_subjects_by_category')) {
    function get_subjects_by_category(int|string $categoryId, int $paginate = 12, int $limit = 0): Collection|LengthAwarePaginator
    {
        return app(SubjectInterface::class)->getByCategory($categoryId, $paginate, $limit);
    }
}

if (! function_exists('get_subjects_by_tag')) {
    function get_subjects_by_tag(string $slug, int $paginate = 12): Collection|LengthAwarePaginator
    {
        return app(SubjectInterface::class)->getByTag($slug, $paginate);
    }
}

if (! function_exists('get_subjects_by_user')) {
    function get_subjects_by_user(int|string $authorId, int $paginate = 12): Collection|LengthAwarePaginator
    {
        return app(SubjectInterface::class)->getByUserId($authorId, $paginate);
    }
}

if (! function_exists('get_all_subjects')) {
    function get_all_subjects(
        bool $active = true,
        int $perPage = 12,
        array $with = ['slugable', 'categories', 'categories.slugable', 'author']
    ): Collection|LengthAwarePaginator {
        return app(SubjectInterface::class)->getAllSubjects($perPage, $active, $with);
    }
}

if (! function_exists('get_recent_subjects')) {
    function get_recent_subjects(int $limit): Collection|LengthAwarePaginator
    {
        return app(SubjectInterface::class)->getRecentSubjects($limit);
    }
}

if (! function_exists('get_featured_categories')) {
    function get_featured_categories(int $limit, array $with = []): Collection|LengthAwarePaginator
    {
        return app(CategoryInterface::class)->getFeaturedCategories($limit, $with);
    }
}

if (! function_exists('get_all_categories')) {
    function get_all_categories(array $condition = [], array $with = []): Collection|LengthAwarePaginator
    {
        return app(CategoryInterface::class)->getAllCategories($condition, $with);
    }
}

if (! function_exists('get_all_tags')) {
    function get_all_tags(bool $active = true): Collection|LengthAwarePaginator
    {
        return app(TagInterface::class)->getAllTags($active);
    }
}

if (! function_exists('get_popular_tags')) {
    function get_popular_tags(
        int $limit = 10,
        array $with = ['slugable'],
        array $withCount = ['subjects']
    ): Collection|LengthAwarePaginator {
        return app(TagInterface::class)->getPopularTags($limit, $with, $withCount);
    }
}

if (! function_exists('get_popular_subjects')) {
    function get_popular_subjects(int $limit = 10, array $args = []): Collection|LengthAwarePaginator
    {
        return app(SubjectInterface::class)->getPopularSubjects($limit, $args);
    }
}

if (! function_exists('get_popular_categories')) {
    function get_popular_categories(
        int $limit = 10,
        array $with = ['slugable'],
        array $withCount = ['subjects']
    ): Collection|LengthAwarePaginator {
        return app(CategoryInterface::class)->getPopularCategories($limit, $with, $withCount);
    }
}

if (! function_exists('get_category_by_id')) {
    function get_category_by_id(int|string $id): ?BaseModel
    {
        return app(CategoryInterface::class)->getCategoryById($id);
    }
}

if (! function_exists('get_categories')) {
    function get_categories(array $args = []): array
    {
        $indent = Arr::get($args, 'indent', '——');

        $repo = app(CategoryInterface::class);

        $categories = $repo->getCategories(Arr::get($args, 'select', ['*']), [
            'is_default' => 'DESC',
            'order' => 'ASC',
            'created_at' => 'DESC',
        ], Arr::get($args, 'condition', ['status' => BaseStatusEnum::PUBLISHED]));

        $categories = sort_item_with_children($categories);

        foreach ($categories as $category) {
            $depth = (int)$category->depth;
            $indentText = str_repeat($indent, $depth);
            $category->indent_text = $indentText;
        }

        return $categories;
    }
}

if (! function_exists('get_categories_with_children')) {
    function get_categories_with_children(): array
    {
        $categories = app(CategoryInterface::class)
            ->getAllCategoriesWithChildren(['status' => BaseStatusEnum::PUBLISHED], [], ['id', 'name', 'parent_id']);

        return app(SortItemsWithChildrenHelper::class)
            ->setChildrenProperty('child_cats')
            ->setItems($categories)
            ->sort();
    }
}

if (! function_exists('register_subject_format')) {
    function register_subject_format(array $formats): void
    {
        SubjectFormat::registerSubjectFormat($formats);
    }
}

if (! function_exists('get_subject_formats')) {
    function get_subject_formats(bool $toArray = false): array
    {
        return SubjectFormat::getSubjectFormats($toArray);
    }
}

if (! function_exists('get_collection_page_id')) {
    function get_collection_page_id(): string|null
    {
        return theme_option('collection_page_id', setting('collection_page_id'));
    }
}

if (! function_exists('get_collection_page_url')) {
    function get_collection_page_url(): string
    {
        $collectionPageId = (int)theme_option('collection_page_id', setting('collection_page_id'));

        if (! $collectionPageId) {
            return BaseHelper::getHomepageUrl();
        }

        $collectionPage = Page::query()->find($collectionPageId);

        if (! $collectionPage) {
            return BaseHelper::getHomepageUrl();
        }

        return $collectionPage->url;
    }
}
