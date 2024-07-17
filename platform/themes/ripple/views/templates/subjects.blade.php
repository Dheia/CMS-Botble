@if($taxons->isNotEmpty())
    <div class="taxon-list mb-40">
        @foreach ($taxons as $taxon)
            <span class="d-inline-block">
                <a class="btn btn--small btn--black" href="{{ $taxon->url }}">{{ $taxon->name }} </a>
            </span>
        @endforeach
    </div>
@endif

@if ($subjects->isNotEmpty())
    <div class="subject-list grid grid-cols-1 sm:grid-cols-3 gap-4">
        @foreach ($subjects as $subject)
            <article class="subject subject__horizontal mb-40 clearfix">
                <div class="subject__thumbnail">
                    <a
                        class="subject__overlay"
                        href="{{ $subject->url }}"
                        title="{{ $subject->name }}"
                    >
                        {{ RvMedia::image($subject->image, $subject->name, 'medium') }}
                    </a>
                    <div class="subject__meta">
                        {!! Theme::partial('collection.subject-meta', compact('subject')) !!}
                    </div>
                </div>
                <div class="subject__content-wrap">
                    <header class="subject__header">
                        <h3 class="subject__title">
                            <a href="{{ $subject->url }}" title="{{ $subject->name }}">
                                {{ $subject->name }}
                            </a>
                        </h3>
                    </header>
                    <div class="subject__content p-0">
                        <p data-number-line="4">{{ $subject->description }}</p>
                    </div>
                    <div class="subject__footer">
                        <a href="{{ $subject->website }}" target="_blank">{!! BaseHelper::renderIcon('ti ti-external-link') !!} {{ $subject->website }}</a>
                    </div>
                </div>
            </article>
        @endforeach
    </div>
    <div class="page-pagination text-right">
        {!! $subjects->withQueryString()->links() !!}
    </div>
@endif

<style>
    .section.grid-template {
        background-color: #ecf0f1;
    }
</style>