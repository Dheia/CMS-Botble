@php
    if (!isset($groupedTaxons)) {
        $groupedTaxons = $taxons->groupBy('parent_id');
    }

    $currentTaxons = $groupedTaxons->get($parentId = $parentId ?? 0);
@endphp

@if ($currentTaxons)
    <ol @class(['list-group dd-list', $className ?? null])>
        @foreach ($currentTaxons as $taxon)
            @php
                $hasChildren = $groupedTaxons->has($taxon->id);
            @endphp
            <li class="dd-item" data-id="{{ $taxon->id }}" data-name="{{ $taxon->name }}">
                @if($updateTreeRoute)
                    <div class="dd-handle dd3-handle"></div>
                @endif
                <div @class(['dd3-content d-flex align-items-center gap-2', 'ps-3' => !$updateTreeRoute])>
                    <div class="d-flex align-items-center gap-1" style="width: 90%;">
                        <x-core::icon :name="$hasChildren ? 'ti ti-folder' : 'ti ti-file'" />
                        <span
                            class="fetch-data text-truncate"
                            role="button"
                            data-href="{{ $canEdit && $editRoute ? route($editRoute, $taxon->id) : '' }}"
                        >
                            {{ $taxon->name }}
                        </span>

                        @if($taxon->badge_with_count)
                            {{ $taxon->badge_with_count }}
                        @endif

                        @if ($canDelete)
                            <span
                                data-bs-toggle="modal"
                                data-bs-target=".modal-confirm-delete"
                                data-url="{{ route($deleteRoute, $taxon->id)}}"
                                class="ms-2"
                            >
                            <x-core::button
                                type="button"
                                color="danger"
                                size="sm"
                                class="delete-button"
                                icon="ti ti-trash"
                                :icon-only="true"
                                :tooltip="trans('core/base::tree-taxon.delete_button')"
                                data-bs-placement="right"
                            />
                        </span>
                        @endif
                    </div>
                </div>
                @if ($hasChildren)
                    @include('core/base::forms.partials.tree-taxon', [
                        'groupedTaxons' => $groupedTaxons,
                        'parentId' => $taxon->id,
                        'className' => '',
                    ])
                @endif
            </li>
        @endforeach
    </ol>
@endif
