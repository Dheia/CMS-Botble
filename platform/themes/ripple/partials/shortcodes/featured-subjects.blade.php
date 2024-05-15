@if ($subjects->isNotEmpty())
    <section class="section pt-50 pb-50 bg-lightgray">
        <div class="container">
            <div class="subject-group subject-group--hero">
                @foreach ($subjects as $subject)
                    @if ($loop->first)
                        <div class="subject-group__left">
                            <article class="subject subject__inside subject__inside--feature">
                                <div class="subject__thumbnail">
                                    {{ RvMedia::image($subject->image, $subject->name, 'featured', attributes: ['loading' => 'eager']) }}
                                    <a
                                        class="subject__overlay"
                                        href="{{ $subject->url }}"
                                        title="{{ $subject->name }}"
                                    ></a>
                                </div>
                                <header class="subject__header">
                                    <h3 class="subject__title text-truncate"><a
                                            href="{{ $subject->url }}">{{ $subject->name }}</a></h3>
                                    <div class="subject__meta">
                                        {!! Theme::partial('collection.subject-meta', compact('subject')) !!}
                                    </div>
                                </header>
                            </article>
                        </div>
                        <div class="subject-group__right">
                        @else
                            <div class="subject-group__item">
                                <article class="subject subject__inside subject__inside--feature subject__inside--feature-small">
                                    <div class="subject__thumbnail">
                                        {{ RvMedia::image($subject->image, $subject->name, 'medium', attributes: ['loading' => 'eager']) }}
                                        <a
                                            class="subject__overlay"
                                            href="{{ $subject->url }}"
                                            title="{{ $subject->name }}"
                                        ></a>
                                    </div>
                                    <header class="subject__header">
                                        <h3 class="subject__title text-truncate"><a
                                                href="{{ $subject->url }}">{{ $subject->name }}</a>
                                        </h3>
                                    </header>
                                </article>
                            </div>
                            @if ($loop->last)
                        </div>
                    @endif
                @endif
@endforeach
</div>
</div>
</section>
@endif
