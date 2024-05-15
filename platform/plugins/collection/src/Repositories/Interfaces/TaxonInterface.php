<?php

namespace Botble\Collection\Repositories\Interfaces;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Models\BaseModel;
use Botble\Collection\Models\Taxon;
use Botble\Support\Repositories\Interfaces\RepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface TaxonInterface extends RepositoryInterface
{
    public function getDataSiteMap(): Collection;

    public function getFeaturedTaxon(int|null $limit, array $with = []): Collection;

    public function getAllTaxon(array $condition = [], array $with = []): Collection;

    public function getTaxonById(int|string|null $id): ?Taxon;

    public function getTaxon(array $select, array $orderBy, array $conditions = ['status' => BaseStatusEnum::PUBLISHED]): Collection;

    public function getAllRelatedChildrenIds(int|string|null|BaseModel $id): array;

    public function getAllTaxonWithChildren(array $condition = [], array $with = [], array $select = ['*']): Collection;

    public function getFilters(array $filters): LengthAwarePaginator;

    public function getPopularTaxon(int $limit, array $with = ['slugable'], array $withCount = ['subjects']): Collection;
}
