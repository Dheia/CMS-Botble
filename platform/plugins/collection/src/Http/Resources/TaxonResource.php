<?php

namespace Botble\Collection\Http\Resources;

use Botble\Collection\Models\Taxon;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Taxon
 */
class TaxonResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'url' => $this->url,
            'description' => $this->description,
        ];
    }
}
