<div class="row mb-40 list-style-2">
    <div class="col-md-4">
        <div class="subject-thumb position-relative border-radius-5">
            <div class="img-hover-slide border-radius-5 position-relative" style="background-image: url({{ RvMedia::getImageUrl($subject->image, null, false, RvMedia::getDefaultImage())}})">
                <a class="img-link" href="{{ $subject->url }}"></a>
            </div>
        </div>
    </div>
    <div class="col-md-8 align-self-center">
        <div class="subject-content">
            @if ($subject->taxons->first())
                <div class="entry-meta meta-0 font-small mb-10">
                    <a href="{{ $subject->taxons->first()->url }}"><span class="subject-cat {{ random_color() }}">{{ $subject->taxons->first()->name }}</span></a>
                </div>
            @endif
            <h5 class="subject-title font-weight-900 mb-20">
                <a href="{{ $subject->url }}">{{ $subject->name }}</a>
                <span class="subject-format-icon"><i class="elegant-icon icon_star_alt"></i></span>
            </h5>
            <div class="entry-meta meta-1 float-left font-x-small text-uppercase">
                <span class="subject-on">{{ $subject->created_at->translatedFormat('M d, Y') }}</span>
                <span class="subject-by has-dot">{{ number_format($subject->views) }} {{ __('views') }}</span>
            </div>
        </div>
    </div>
</div>
