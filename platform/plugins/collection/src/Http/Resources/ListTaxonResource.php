<?php

namespace Botble\Collection\Http\Resources;

use Botble\Collection\Models\Taxon;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Taxon
 */
class ListTaxonResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'children' => TaxonResource::collection($this->children),
            'parent' => new TaxonResource($this->parent),
        ];
    }
}
