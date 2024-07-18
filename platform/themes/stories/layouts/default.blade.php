{!! Theme::partial('header') !!}

<div class="container single-content">
    @if (Theme::get('pageId') && !BaseHelper::isHomepage(Theme::get('pageId')))
        <div class="archive-header pt-50">
            <h2 class="font-weight-900">{{ Theme::get('title') ?: SeoHelper::getTitle() }}</h2>
            @if (Theme::get('subtitle'))
                <p>{!! Theme::get('subtitle') !!}</p>
            @endif
            {!! Theme::partial('breadcrumbs') !!}
            <div class="bt-1 border-color-1 mt-30 mb-50"></div>
        </div>
    @endif

    <article class="entry-wraper mb-50">
        <div class="entry-main-content">
             {!! Theme::content() !!}
        </div>
    </article>
</div>

{!! Theme::partial('footer') !!}
