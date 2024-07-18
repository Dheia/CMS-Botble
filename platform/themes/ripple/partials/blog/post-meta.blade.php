@if ($post->first_category?->name)
    <span class="post-category">
        <a href="{{ $post->first_category->url }}">
        {!! BaseHelper::renderIcon('ti ti-cube') !!} {{ $post->first_category->name }}
        </a>
    </span>
@endif

<span class="created_at">
    {!! BaseHelper::renderIcon('ti ti-clock') !!} {{ $post->created_at->translatedFormat('M d Y') }}
</span>

@if ($post->author->name)
    <span class="post-author">{!! BaseHelper::renderIcon('ti ti-user-circle') !!} <span>{{ $post->author->name }}</span></span>
@endif
