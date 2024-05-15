<?php

namespace Botble\Collection\Repositories\Eloquent;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Models\BaseModel;
use Botble\Collection\Models\Taxon;
use Botble\Collection\Repositories\Interfaces\TaxonInterface;
use Botble\Support\Repositories\Eloquent\RepositoriesAbstract;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class TaxonRepository extends RepositoriesAbstract implements TaxonInterface
{
    public function getDataSiteMap(): Collection
    {
        $data = $this->model
            ->with('slugable')
            ->wherePublished()
            ->select(['id', 'name', 'updated_at'])
            ->orderByDesc('created_at');

        return $this->applyBeforeExecuteQuery($data)->get();
    }

    public function getFeaturedTaxon(int|null $limit, array $with = []): Collection
    {
        $data = $this->model
            ->with(array_merge(['slugable'], $with))
            ->where([
                'status' => BaseStatusEnum::PUBLISHED,
                'is_featured' => 1,
            ])
            ->select([
                'id',
                'name',
                'description',
                'icon',
            ])
            ->orderBy('order')
            ->orderByDesc('created_at')
            ->limit($limit);

        return $this->applyBeforeExecuteQuery($data)->get();
    }

    public function getAllTaxon(array $condition = [], array $with = []): Collection
    {
        $data = $this->model->with('slugable');
        if (! empty($condition)) {
            $data = $data->where($condition);
        }

        $data = $data
            ->wherePublished()
            ->orderBy('order')
            ->orderByDesc('created_at');

        if ($with) {
            $data = $data->with($with);
        }

        return $this->applyBeforeExecuteQuery($data)->get();
    }

    public function getTaxonById(int|string|null $id): ?Taxon
    {
        $data = $this->model->with('slugable')->where([
            'id' => $id,
            'status' => BaseStatusEnum::PUBLISHED,
        ]);

        return $this->applyBeforeExecuteQuery($data, true)->first();
    }

    public function getTaxon(array $select, array $orderBy, array $conditions = ['status' => BaseStatusEnum::PUBLISHED]): Collection
    {
        $data = $this->model
            ->with('slugable')
            ->select($select);

        if ($conditions) {
            $data = $data->where($conditions);
        }

        foreach ($orderBy as $by => $direction) {
            $data = $data->orderBy($by, $direction);
        }

        return $this->applyBeforeExecuteQuery($data)->get();
    }

    public function getAllRelatedChildrenIds(int|string|null|BaseModel $id): array
    {
        if ($id instanceof BaseModel) {
            $model = $id;
        } else {
            $model = $this->getFirstBy(['id' => $id]);
        }

        if (! $model) {
            return [];
        }

        $result = [];

        $children = $model->children()->select('id')->get();

        foreach ($children as $child) {
            $result[] = $child->id;
            $result = array_merge($this->getAllRelatedChildrenIds($child), $result);
        }

        $this->resetModel();

        return array_unique($result);
    }

    public function getAllTaxonWithChildren(array $condition = [], array $with = [], array $select = ['*']): Collection
    {
        $data = $this->model
            ->where($condition)
            ->with($with)
            ->select($select);

        return $this->applyBeforeExecuteQuery($data)->get();
    }

    public function getFilters(array $filters): LengthAwarePaginator
    {
        $orderBy = $filters['order_by'] ?? 'created_at';

        $order = $filters['order'] ?? 'ASC';

        $data = $this->model->wherePublished()->orderBy($orderBy, $order);

        return $this->applyBeforeExecuteQuery($data)->paginate((int)$filters['per_page']);
    }

    public function getPopularTaxon(int $limit, array $with = ['slugable'], array $withCount = ['subjects']): Collection
    {
        $data = $this->model
            ->with($with)
            ->withCount($withCount)
            ->orderByDesc('subjects_count')
            ->orderBy('order')
            ->orderByDesc('created_at')
            ->wherePublished()
            ->limit($limit);

        return $this->applyBeforeExecuteQuery($data)->get();
    }
}
