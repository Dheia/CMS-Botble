{!! BaseHelper::googleFonts('https://fonts.googleapis.com/css2?family=' . urlencode(theme_option('primary_font', 'Roboto')) . ':wght@400;500;600;700&display=swap') !!}

<style>
    :root {
        --color-1st: {{ theme_option('primary_color', '#2E76E2') }};
        --primary-color: {{ theme_option('primary_color', '#2E76E2') }};
        --primary-font: '{{ theme_option('primary_font', 'Roboto') }}', sans-serif;
    }
</style>

@php
    Theme::asset()->container('footer')->remove('simple-slider-js');
@endphp
