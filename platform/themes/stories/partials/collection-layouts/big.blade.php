<div class="loop-list loop-list-style-1">
    <div class="row">
        @foreach($subjects as $subject)
            <article class="col-md-{{ Theme::getLayoutName() == 'right-sidebar' ? 6 : 4 }} mb-40 wow fadeInUp animated">
                <div class="subject-card-1 border-radius-10 hover-up">
                    <div class="subject-thumb thumb-overlay img-hover-slide position-relative" style="background-image: url({{ RvMedia::getImageUrl($subject->image, null, false, RvMedia::getDefaultImage()) }})">
                        <a class="img-link" href="{{ $subject->url }}"></a>
                    </div>
                    <div class="subject-content p-30">
                        <div class="entry-meta meta-0 font-small mb-10">
                            @foreach($subject->taxons as $taxon)
                                <a href="{{ $taxon->url }}"><span class="subject-cat {{ random_color() }}">{{ $taxon->name }}</span></a>
                            @endforeach
                        </div>
                        <div class="d-flex subject-card-content">
                            <h5 class="subject-title mb-20 font-weight-900">
                                <a href="{{ $subject->url }}">{{ $subject->name }}</a>
                            </h5>
                            <div class="subject-excerpt mb-25 font-small text-muted">
                                <p>{{ $subject->description }}</p>
                            </div>
                            <div class="entry-meta meta-1 float-left font-x-small text-uppercase">
                                <span class="subject-on">{{ $subject->created_at->translatedFormat('M d, Y') }}</span>
                                <span class="subject-by has-dot">{{ number_format($subject->views) }} {{ __('views') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </article>
        @endforeach
    </div>
</div>

<div class="pagination-area mb-30 wow fadeInUp animated justify-content-start">
    {!! $subjects->withQueryString()->onEachSide(1)->links() !!}
</div>
