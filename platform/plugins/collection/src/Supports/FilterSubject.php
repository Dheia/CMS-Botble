<?php

namespace Botble\Collection\Supports;

use Botble\Base\Enums\BaseStatusEnum;

class FilterSubject
{
    public static function setFilters(array $request): array
    {
        if (isset($request['order'])) {
            $request['order'] = strtolower($request['order']);
        }

        return [
            'page' => $request['page'] ?? 1,
            'per_page' => $request['per_page'] ?? 10,
            'search' => $request['search'] ?? null,
            'author' => $request['author'] ?? null,
            'author_exclude' => $request['author_exclude'] ?? null,
            'exclude' => $request['exclude'] ?? null,
            'include' => $request['include'] ?? null,
            'after' => $request['after'] ?? null,
            'before' => $request['before'] ?? null,
            'order' => isset($request['order']) && in_array($request['order'], ['asc', 'desc']) ? $request['order'] : 'desc',
            'order_by' => $request['order_by'] ?? 'updated_at',
            'status' => BaseStatusEnum::PUBLISHED,
            'taxon' => $request['taxon'] ?? null,
            'taxon_exclude' => $request['taxon_exclude'] ?? null,
            'tags' => $request['tags'] ?? null,
            'tags_exclude' => $request['tags_exclude'] ?? null,
            'featured' => $request['featured'] ?? null,
        ];
    }
}
