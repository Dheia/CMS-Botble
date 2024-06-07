{!! Theme::partial('header') !!}

@if (Theme::get('section-name'))
    {!! Theme::partial('breadcrumbs') !!}
@endif

<section class="section grid-template pt-50 pb-100">
    <div class="container">
        {!! Theme::content() !!}
    </div>
</section>

{!! Theme::partial('footer') !!}
