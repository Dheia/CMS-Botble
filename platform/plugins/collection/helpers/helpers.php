<?php

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Facades\BaseHelper;
use Botble\Base\Models\BaseModel;
use Botble\Base\Supports\SortItemsWithChildrenHelper;
use Botble\Collection\Repositories\Interfaces\TaxonInterface;
use Botble\Collection\Repositories\Interfaces\SubjectInterface;
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

if (! function_exists('get_subjects_by_taxon')) {
    function get_subjects_by_taxon(int|string $taxonId, int $paginate = 12, int $limit = 0): Collection|LengthAwarePaginator
    {
        return app(SubjectInterface::class)->getByTaxon($taxonId, $paginate, $limit);
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
        array $with = ['slugable', 'taxons', 'taxons.slugable', 'author']
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

if (! function_exists('get_featured_taxons')) {
    function get_featured_taxons(int $limit, array $with = []): Collection|LengthAwarePaginator
    {
        return app(TaxonInterface::class)->getFeaturedTaxons($limit, $with);
    }
}

if (! function_exists('get_all_taxons')) {
    function get_all_taxons(array $condition = [], array $with = []): Collection|LengthAwarePaginator
    {
        return app(TaxonInterface::class)->getAllTaxons($condition, $with);
    }
}

if (! function_exists('get_popular_subjects')) {
    function get_popular_subjects(int $limit = 10, array $args = []): Collection|LengthAwarePaginator
    {
        return app(SubjectInterface::class)->getPopularSubjects($limit, $args);
    }
}

if (! function_exists('get_popular_taxons')) {
    function get_popular_taxons(
        int $limit = 10,
        array $with = ['slugable'],
        array $withCount = ['subjects']
    ): Collection|LengthAwarePaginator {
        return app(TaxonInterface::class)->getPopularTaxons($limit, $with, $withCount);
    }
}

if (! function_exists('get_taxon_by_id')) {
    function get_taxon_by_id(int|string $id): ?BaseModel
    {
        return app(TaxonInterface::class)->getTaxonById($id);
    }
}

if (! function_exists('get_taxons')) {
    function get_taxons(array $args = []): array
    {
        $indent = Arr::get($args, 'indent', '——');

        $repo = app(TaxonInterface::class);

        $taxons = $repo->getTaxons(Arr::get($args, 'select', ['*']), [
            'is_default' => 'DESC',
            'order' => 'ASC',
            'created_at' => 'DESC',
        ], Arr::get($args, 'condition', ['status' => BaseStatusEnum::PUBLISHED]));

        $taxons = sort_item_with_children($taxons);

        foreach ($taxons as $taxon) {
            $depth = (int)$taxon->depth;
            $indentText = str_repeat($indent, $depth);
            $taxon->indent_text = $indentText;
        }

        return $taxons;
    }
}

if (! function_exists('get_taxons_with_children')) {
    function get_taxons_with_children(): array
    {
        $taxons = app(TaxonInterface::class)
            ->getAllTaxonsWithChildren(['status' => BaseStatusEnum::PUBLISHED], [], ['id', 'name', 'parent_id']);

        return app(SortItemsWithChildrenHelper::class)
            ->setChildrenProperty('child_cats')
            ->setItems($taxons)
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

if (! function_exists('is_collection_page')) {
    function is_collection_page(int|string|null $pageId = null): bool
    {
        $collectionPageId = get_collection_page_id();
        return $pageId && $collectionPageId && $pageId == $collectionPageId;
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
