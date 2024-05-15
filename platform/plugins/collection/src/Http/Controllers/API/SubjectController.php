<?php

namespace Botble\Collection\Http\Controllers\API;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Facades\BaseHelper;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Collection\Http\Resources\ListSubjectResource;
use Botble\Collection\Http\Resources\SubjectResource;
use Botble\Collection\Models\Subject;
use Botble\Collection\Repositories\Interfaces\SubjectInterface;
use Botble\Collection\Supports\FilterSubject;
use Botble\Slug\Facades\SlugHelper;
use Illuminate\Http\Request;

class SubjectController extends BaseController
{
    public function __construct(protected SubjectInterface $subjectRepository)
    {
    }

    /**
     * List subjects
     *
     * @group Collection
     */
    public function index(Request $request)
    {
        $data = $this->subjectRepository
            ->advancedGet([
                'with' => ['taxon', 'author', 'slugable'],
                'condition' => ['status' => BaseStatusEnum::PUBLISHED],
                'paginate' => [
                    'per_page' => $request->integer('per_page', 10),
                    'current_paged' => $request->integer('page', 1),
                ],
            ]);

        return $this
            ->httpResponse()
            ->setData(ListSubjectResource::collection($data))
            ->toApiResponse();
    }

    /**
     * Search subject
     *
     * @bodyParam q string required The search keyword.
     *
     * @group Collection
     */
    public function getSearch(Request $request, SubjectInterface $subjectRepository)
    {
        $query = BaseHelper::stringify($request->input('q'));
        $subjects = $subjectRepository->getSearch($query);

        $data = [
            'items' => $subjects,
            'query' => $query,
            'count' => $subjects->count(),
        ];

        if ($data['count'] > 0) {
            return $this
                ->httpResponse()
                ->setData(apply_filters(BASE_FILTER_SET_DATA_SEARCH, $data));
        }

        return $this
            ->httpResponse()
            ->setError()
            ->setMessage(trans('core/base::layouts.no_search_result'));
    }

    /**
     * Filters subjects
     *
     * @group Collection
     * @queryParam page                 Current page of the collection. Default: 1
     * @queryParam per_page             Maximum number of items to be returned in result set.Default: 10
     * @queryParam search               Limit results to those matching a string.
     * @queryParam after                Limit response to subjects published after a given ISO8601 compliant date.
     * @queryParam author               Limit result set to subjects assigned to specific authors.
     * @queryParam author_exclude       Ensure result set excludes subjects assigned to specific authors.
     * @queryParam before               Limit response to subjects published before a given ISO8601 compliant date.
     * @queryParam exclude              Ensure result set excludes specific IDs.
     * @queryParam include              Limit result set to specific IDs.
     * @queryParam order                Order sort attribute ascending or descending. Default: desc .One of: asc, desc
     * @queryParam order_by             Sort collection by object attribute. Default: updated_at. One of: author, created_at, updated_at, id,  slug, title
     * @queryParam taxon           Limit result set to all items that have the specified term assigned in the taxon taxonomy.
     * @queryParam taxon_exclude   Limit result set to all items except those that have the specified term assigned in the taxon taxonomy.
     * @queryParam featured             Limit result set to items that are sticky.
     */
    public function getFilters(Request $request)
    {
        $filters = FilterSubject::setFilters($request->input());

        $data = $this->subjectRepository->getFilters($filters);

        return $this
            ->httpResponse()
            ->setData(ListSubjectResource::collection($data))
            ->toApiResponse();
    }

    /**
     * Get subject by slug
     *
     * @group Collection
     * @queryParam slug Find by slug of subject.
     */
    public function findBySlug(string $slug)
    {
        $slug = SlugHelper::getSlug($slug, SlugHelper::getPrefix(Subject::class));

        if (! $slug) {
            return $this
                ->httpResponse()
                ->setError()
                ->setCode(404)
                ->setMessage('Not found');
        }

        $subject = Subject::query()
            ->where([
                'id' => $slug->reference_id,
                'status' => BaseStatusEnum::PUBLISHED,
            ])
            ->first();

        if (! $subject) {
            return $this
                ->httpResponse()
                ->setError()
                ->setCode(404)
                ->setMessage('Not found');
        }

        return $this
            ->httpResponse()
            ->setData(new SubjectResource($subject))
            ->toApiResponse();
    }
}
