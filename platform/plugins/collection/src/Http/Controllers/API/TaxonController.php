<?php

namespace Botble\Collection\Http\Controllers\API;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Collection\Http\Resources\TaxonResource;
use Botble\Collection\Http\Resources\ListTaxonResource;
use Botble\Collection\Models\Taxon;
use Botble\Collection\Repositories\Interfaces\TaxonInterface;
use Botble\Collection\Supports\FilterTaxon;
use Botble\Slug\Facades\SlugHelper;
use Illuminate\Http\Request;

class TaxonController extends BaseController
{
    /**
     * List taxon
     *
     * @group Collection
     */
    public function index(Request $request)
    {
        $data = Taxon::query()
            ->wherePublished()
            ->orderByDesc('created_at')
            ->with(['slugable'])
            ->paginate($request->integer('per_page', 10) ?: 10);

        return $this
            ->httpResponse()
            ->setData(ListTaxonResource::collection($data))
            ->toApiResponse();
    }

    /**
     * Filters taxon
     *
     * @group Collection
     */
    public function getFilters(Request $request, TaxonInterface $taxonRepository)
    {
        $filters = FilterTaxon::setFilters($request->input());
        $data = $taxonRepository->getFilters($filters);

        return $this
            ->httpResponse()
            ->setData(TaxonResource::collection($data))
            ->toApiResponse();
    }

    /**
     * Get taxon by slug
     *
     * @group Collection
     * @queryParam slug Find by slug of taxon.
     */
    public function findBySlug(string $slug)
    {
        $slug = SlugHelper::getSlug($slug, SlugHelper::getPrefix(Taxon::class));

        if (! $slug) {
            return $this
                ->httpResponse()
                ->setError()
                ->setCode(404)
                ->setMessage('Not found');
        }

        $taxon = Taxon::query()
            ->with('slugable')
            ->where([
                'id' => $slug->reference_id,
                'status' => BaseStatusEnum::PUBLISHED,
            ])
            ->first();

        if (! $taxon) {
            return $this
                ->httpResponse()
                ->setError()
                ->setCode(404)
                ->setMessage('Not found');
        }

        return $this
            ->httpResponse()
            ->setData(new ListTaxonResource($taxon))
            ->toApiResponse();
    }
}
