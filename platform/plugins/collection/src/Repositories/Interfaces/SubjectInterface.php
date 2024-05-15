<?php

namespace Botble\Collection\Repositories\Interfaces;

use Botble\Collection\Models\Subject;
use Botble\Support\Repositories\Interfaces\RepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface SubjectInterface extends RepositoryInterface
{
    public function getFeatured(int $limit = 5, array $with = []): Collection;

    public function getListSubjectNonInList(array $selected = [], int $limit = 7, array $with = []): Collection;

    public function getRelated(int|string $id, int $limit = 3): Collection;

    public function getRelatedTaxonIds(Subject|int|string $model): array;

    public function getByTaxon(array|int|string $taxonId, int $paginate = 12, int $limit = 0): Collection|LengthAwarePaginator;

    public function getByUserId(int|string $authorId, int $paginate = 6): Collection|LengthAwarePaginator;

    public function getDataSiteMap(): Collection|LengthAwarePaginator;

    public function getRecentSubjects(int $limit = 5, int|string $taxonId = 0): Collection;

    public function getSearch(string|null $keyword, int $limit = 10, int $paginate = 10): Collection|LengthAwarePaginator;

    public function getAllSubjects(int $perPage = 12, bool $active = true, array $with = ['slugable']): Collection|LengthAwarePaginator;

    public function getPopularSubjects(int $limit, array $args = []): Collection;

    public function getFilters(array $filters): Collection|LengthAwarePaginator;
}
