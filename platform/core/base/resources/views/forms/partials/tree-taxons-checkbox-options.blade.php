@if ($taxons)
    @php
        $selected = (array) $selected;
    @endphp

    <ul @class(['list-unstyled', $class ?? null])>
        @foreach ($taxons as $taxon)
            @continue($taxon->id === $currentId)

            <li>
                <x-core::form.checkbox
                    :label="$taxon->name"
                    :name="$name"
                    :value="$taxon->id"
                    :checked="in_array($taxon->id, $selected)"
                />

                @if ($taxon->activeChildren->isNotEmpty())
                    @include('core/base::forms.partials.tree-taxons-checkbox-options', [
                        'taxons' => $taxon->activeChildren,
                        'selected' => $selected,
                        'currentId' => $currentId,
                        'name' => $name,
                        'class' => 'ms-4 mt-2'
                    ])
                @endif
            </li>
        @endforeach
    </ul>
@endif
