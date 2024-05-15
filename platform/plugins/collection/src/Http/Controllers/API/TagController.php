<?php

namespace Botble\Collection\Http\Controllers\API;

use Botble\Base\Http\Controllers\BaseController;
use Botble\Collection\Http\Resources\TagResource;
use Botble\Collection\Models\Tag;
use Illuminate\Http\Request;

class TagController extends BaseController
{
    /**
     * List tags
     *
     * @group Collection
     */
    public function index(Request $request)
    {
        $data = Tag::query()
            ->wherePublished()
            ->with('slugable')
            ->paginate($request->integer('per_page', 10) ?: 10);

        return $this
            ->httpResponse()
            ->setData(TagResource::collection($data))
            ->toApiResponse();
    }
}
