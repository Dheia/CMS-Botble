@php Theme::set('section-name', $taxon->name) @endphp

@if ($subjects->isNotEmpty())
    @foreach ($subjects->loadMissing('author') as $subject)
        <article class="subject subject__horizontal mb-40 clearfix">
            <div class="subject__thumbnail">
                {{ RvMedia::image($subject->image, $subject->name, 'medium') }}
                <a href="{{ $subject->url }}" title="{{ $subject->name }}" class="subject__overlay"></a>
            </div>
            <div class="subject__content-wrap">
                <header class="subject__header">
                    <h3 class="subject__title"><a href="{{ $subject->url }}" title="{{ $subject->name }}">{{ $subject->name }}</a></h3>
                    <div class="subject__meta">
                        {!! Theme::partial('collection.subject-meta', compact('subject')) !!}
                    </div>
                </header>
                <div class="subject__content">
                    <p data-number-line="4">{{ $subject->description }}</p>
                </div>
            </div>
        </article>
    @endforeach
    <div class="page-pagination text-right">
        {!! $subjects->links() !!}
    </div>
@else
    <div class="alert alert-warning">
        <p class="mb-0">{{ __('There is no data to display!') }}</p>
    </div>
@endif
