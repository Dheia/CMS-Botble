@if ($subjects->isNotEmpty())
    @foreach ($subjects as $subject)
        <article class="subject subject__horizontal mb-40 clearfix">
            <div class="subject__thumbnail">
                {{ RvMedia::image($subject->image, $subject->name, 'medium') }}
                <a
                    class="subject__overlay"
                    href="{{ $subject->url }}"
                    title="{{ $subject->name }}"
                ></a>
            </div>
            <div class="subject__content-wrap">
                <header class="subject__header">
                    <h3 class="subject__title"><a
                            href="{{ $subject->url }}"
                            title="{{ $subject->name }}"
                        >{{ $subject->name }}</a></h3>
                    <div class="subject__meta">
                        {!! Theme::partial('collection.subject-meta', compact('subject')) !!}
                    </div>
                </header>
                <div class="subject__content p-0">
                    <p data-number-line="4">{{ $subject->description }}</p>
                </div>
            </div>
        </article>
    @endforeach
    <div class="page-pagination text-right">
        {!! $subjects->withQueryString()->links() !!}
    </div>
@endif

<style>
    .section.pt-50.pb-100 {
        background-color: #ecf0f1;
    }
</style>
