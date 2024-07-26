@php
    Theme::set('pageId', $page->id);
@endphp

@if (BaseHelper::isHomepage($page->id))
    @if (defined('GALLERY_MODULE_SCREEN_NAME') && !empty($galleries = gallery_meta_data($page)))
        {!! render_object_gallery($galleries) !!}
    @endif
    {!! apply_filters(
        PAGE_FILTER_FRONT_PAGE_CONTENT, 
        Html::tag('div', BaseHelper::clean($page->content), ['class' => 'ck-content'])->toHtml(), 
        $page
    ) !!} 
@else
    @php
        Theme::set('section-name', SeoHelper::getTitle());
        $page->loadMissing('metadata');

        $bannerImage = $page->getMetaData('banner_image', true);

        if ($bannerImage) {
            Theme::set('breadcrumbBannerImage', RvMedia::getImageUrl($bannerImage));
        }

        $pageType = 'page';
        if(is_blog_page($page->id)){
            $pageType = 'blog';
        } else if(is_collection_page($page->id)){
            $pageType = 'collection';
            Theme::set('search-type', 'collection');
        }
    @endphp
    <div class="{{ $pageType . ' ' . $pageType . '--list' }}">
        <div class="{{ $pageType . '__content' }}">
            @if (defined('GALLERY_MODULE_SCREEN_NAME') && !empty($galleries = gallery_meta_data($page)))
                {!! render_object_gallery($galleries) !!}
            @endif
            {!! apply_filters(
                PAGE_FILTER_FRONT_PAGE_CONTENT, 
                Html::tag('div', BaseHelper::clean($page->content), ['class' => 'ck-content'])->toHtml(), 
                $page
            ) !!}
        </div>
    </div>
@endif
