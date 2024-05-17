@php
    $updateTreeRoute ??= null;
@endphp

<div class="dd" data-depth="0" data-empty-text="{{ trans('core/base::tree-taxon.empty_text') }}">
    @include('core/base::forms.partials.tree-taxon', compact('updateTreeRoute'))
</div>
