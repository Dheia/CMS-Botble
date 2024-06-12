<div class="subject-card-1 border-radius-10 hover-up">
    <div class="subject-thumb thumb-overlay img-hover-slide position-relative" style="background-image: url({{ RvMedia::getImageUrl($subject->image, null, false, RvMedia::getDefaultImage())}})">
        <a class="img-link" href="{{ $subject->url }}"></a>
    </div>
    <div class="subject-content p-30">
        @if ($subject->taxons->first())
        <div class="entry-meta meta-0 font-small mb-10">
            <a href="{{ $subject->taxons->first()->url }}"><span class="subject-cat {{ random_color() }}">{{ $subject->taxons->first()->name }}</span></a>
        </div>
        @endif
        <div class="d-flex subject-card-content mt-sm-3">
            <h5 class="subject-title mb-20 font-weight-900">
                <a href="{{ $subject->url }}">{{ $subject->name }}</a>
            </h5>
            <div class="entry-meta meta-1 float-left font-x-small text-uppercase">
                <span class="subject-on">{{ $subject->created_at->translatedFormat('M d, Y') }}</span>
                <span class="subject-by has-dot">{{ number_format($subject->views) }} {{ __('views') }}</span>
            </div>
        </div>
    </div>
</div>
