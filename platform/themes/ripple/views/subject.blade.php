@php
    Theme::set('no-sidebar', true);
    Theme::set('section-name', $subject->name);
    $subject->loadMissing('metadata');

    if ($bannerImage = $subject->getMetaData('banner_image', true)) {
        Theme::set('breadcrumbBannerImage', RvMedia::getImageUrl($bannerImage));
    }
@endphp

<article class="subject subject--single">
    <header class="subject__header">
        <h1 class="subject__title">{{ $subject->name }}</h1>
    </header>
    <div class="subject__content">
        @if (defined('GALLERY_MODULE_SCREEN_NAME') && !empty($galleries = gallery_meta_data($subject)))
            {!! render_object_gallery($galleries, ($subject->first_taxon ? $subject->first_taxon->name : __('Uncategorized'))) !!}
        @endif
        <div class="row">
            <div class="col-lg-2">
                {{ RvMedia::image($subject->image, $subject->name, 'thumb') }}
            </div>
            <div class="col-lg-7">
                <div class="subject-website">
                    <a href="{{ $subject->website }}" target="_blank">
                        {!! BaseHelper::renderIcon('ti ti-external-link') !!} {{ $subject->website }}
                    </a>
                </div>
                <div class="subject-description">{!! BaseHelper::clean($subject->description) !!}</div>
                <div class="subject__meta">
                    {!! Theme::partial('collection.subject-meta', compact('subject')) !!}
                </div>
            </div>
            <div class="col-lg-3">
                ADS
            </div>
        </div>
        <div class="fb-like" data-href="{{ request()->url() }}" data-layout="standard" data-action="like" data-show-faces="false" data-share="true"></div>
    </div>
    @php $relatedSubjects = get_related_subjects($subject->id, 2); @endphp

    @if ($relatedSubjects->isNotEmpty())
        <footer class="subject__footer">
            <div class="row">
                @foreach ($relatedSubjects as $relatedItem)
                    <div class="col-md-6 col-sm-6 col-12">
                        <div class="subject__relate-group @if ($loop->last) subject__relate-group--right text-end @else text-start @endif">
                            <h4 class="relate__title">@if ($loop->first) {{ __('Previous Subject') }} @else {{ __('Next Subject') }} @endif</h4>
                            <div class="subject subject--related">
                                <div class="subject__thumbnail">
                                    <a href="{{ $relatedItem->url }}" title="{{ $relatedItem->name }}" class="subject__overlay"></a>
                                    {{ RvMedia::image($relatedItem->image, $relatedItem->name, 'thumb') }}
                                </div>
                                <div class="subject__header">
                                    <p><a href="{{ $relatedItem->url }}" class="subject__title"> {{ $relatedItem->name }}</a></p>
                                    <div class="subject__meta">
                                        <span class="subject__created-at">
                                            {{ $subject->created_at->translatedFormat('M d, Y') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </footer>
    @endif

    {!! apply_filters(BASE_FILTER_PUBLIC_COMMENT_AREA, null, $subject) !!}
</article>
