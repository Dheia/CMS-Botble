@php
    if (!isset($layout)) {
        $layout = theme_option('collection_layout', 'grid');
        $layout = ($layout && in_array($layout, array_keys(get_collection_layouts()))) ? $layout : 'grid';
    }

    if (in_array($layout, ['grid', 'list'])) {
        Theme::layout('right-sidebar');
    }
@endphp

{!! Theme::partial('collection-layouts.' . $layout, compact('subjects')) !!}
