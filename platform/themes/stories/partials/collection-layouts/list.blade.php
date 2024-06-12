<div class="subject-module-3">
    <div class="loop-list loop-list-style-1">
        @foreach($subjects as $subject)
            <article class="hover-up-2 transition-normal wow fadeInUp animated">
                <div class="row mb-40 list-style-2">
                    <div class="col-md-4">
                        <div class="subject-thumb position-relative border-radius-5">
                            <div class="img-hover-slide border-radius-5 position-relative" style="background-image: url({{ RvMedia::getImageUrl($subject->image, 'small', false, RvMedia::getDefaultImage()) }})">
                                <a class="img-link" href="{{ $subject->url }}"></a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8 align-self-center">
                        <div class="subject-content">
                            <div class="entry-meta meta-0 font-small mb-10">
                                @foreach($subject->taxons as $taxon)
                                    <a href="{{ $taxon->url }}"><span class="subject-cat {{ random_color() }}">{{ $taxon->name }}</span></a>
                                @endforeach
                            </div>
                            <h5 class="subject-title font-weight-900 mb-20">
                                <a href="{{ $subject->url }}">{{ $subject->name }}</a>
                            </h5>
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

    <div class="pagination-area mb-30 wow fadeInUp animated justify-content-start">
        {!! $subjects->withQueryString()->onEachSide(1)->links() !!}
    </div>
</div>
