{!! Theme::partial('header') !!}

@if (Theme::get('section-name'))
    {!! Theme::partial('breadcrumbs') !!}
@endif

@if(BaseHelper::isHomepage(Theme::get('pageId')))
    {!! Theme::content() !!}
@else
    <section class="section default-template pt-50 pb-100">
        <div class="container">
            @if(Theme::get('no-sidebar'))
                <div class="page-content">
                    {!! Theme::content() !!}
                </div>
            @else
            <div class="row">
                <div class="col-lg-9">
                    <div class="page-content">
                        {!! Theme::content() !!}
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="page-sidebar">
                        {!! Theme::partial('sidebar') !!}
                    </div>
                </div>
            </div>
            @endif
        </div>
    </section>
@endif

{!! Theme::partial('footer') !!}
