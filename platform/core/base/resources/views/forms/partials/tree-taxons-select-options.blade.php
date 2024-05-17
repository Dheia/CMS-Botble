@if($taxons)
    @php
        $selected = (array) $selected;
    @endphp

    @foreach($taxons as $taxon)
        <option
            value="{{ $taxon->id ?? '' }}"
            @selected(in_array($taxon->id, $selected))
            data-render-item="{{ $taxon->name }}"
            data-render-option="{{ $indent }} {{ $taxon->name }}"
        >
            {{ $taxon->name }}
        </option>

        @if($taxon->activeChildren)
            @include('core/base::forms.partials.tree-taxons-select-options', [
                'taxons' => $taxon->activeChildren,
                'selected' => $selected,
                'currentId' => $currentId,
                'name' => $name,
                'indent' => "{$indent}—"
            ])
        @endif
    @endforeach
@endif
