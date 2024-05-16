@php
    Theme::set('section-name', $subject->name);
    $subject->loadMissing('metadata');

    if ($bannerImage = $subject->getMetaData('banner_image', true)) {
        Theme::set('breadcrumbBannerImage', RvMedia::getImageUrl($bannerImage));
    }
@endphp

<article class="subject subject--single">
    <header class="subject__header">
        <h1 class="subject__title">{{ $subject->name }}</h1>
        <div class="subject__meta">
            {!! Theme::partial('collection.subject-meta', compact('subject')) !!}
        </div>
    </header>
    <div class="subject__content">
        @if (defined('GALLERY_MODULE_SCREEN_NAME') && !empty($galleries = gallery_meta_data($subject)))
            {!! render_object_gallery($galleries, ($subject->first_taxon ? $subject->first_taxon->name : __('Uncategorized'))) !!}
        @endif
        <div class="ck-content">{!! BaseHelper::clean($subject->content) !!}</div>
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
                            <article class="subject subject--related">
                                <div class="subject__thumbnail"><a href="{{ $relatedItem->url }}" title="{{ $relatedItem->name }}" class="subject__overlay"></a>
                                    {{ RvMedia::image($relatedItem->image, $relatedItem->name, 'thumb') }}
                                </div>
                                <header class="subject__header">
                                    <p><a href="{{ $relatedItem->url }}" class="subject__title"> {{ $relatedItem->name }}</a></p>
                                    <div class="subject__meta"><span class="subject__created-at">{{ $subject->created_at->translatedFormat('M d, Y') }}</span></div>
                                </header>
                            </article>
                        </div>
                    </div>
                @endforeach
            </div>
        </footer>
    @endif
    <br>
    {!! apply_filters(BASE_FILTER_PUBLIC_COMMENT_AREA, null, $subject) !!}
</article>
