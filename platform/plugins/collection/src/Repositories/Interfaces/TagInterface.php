<?php

namespace Botble\Collection\Repositories\Interfaces;

use Botble\Support\Repositories\Interfaces\RepositoryInterface;
use Illuminate\Support\Collection;

interface TagInterface extends RepositoryInterface
{
    public function getDataSiteMap(): Collection;

    public function getPopularTags(int $limit, array $with = ['slugable'], array $withCount = ['subjects']): Collection;

    public function getAllTags(bool $active = true): Collection;
}
