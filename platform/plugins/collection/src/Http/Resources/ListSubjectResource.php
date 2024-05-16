<?php

namespace Botble\Collection\Http\Resources;

use Botble\Collection\Models\Subject;
use Botble\Media\Facades\RvMedia;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Subject
 */
class ListSubjectResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'image' => $this->image ? RvMedia::url($this->image) : null,
            'taxons' => TaxonResource::collection($this->taxons),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
