<div>
    <h3>{{ $subject->name }}</h3>
    {!! Theme::breadcrumb()->render() !!}
</div>
<header>
    <h3>{{ $subject->name }}</h3>
    <div>
        @if ($subject->taxon->isNotEmpty())
            <span>
                <a href="{{ $subject->taxon->first()->url }}">{{ $subject->taxon->first()->name }}</a>
            </span>
        @endif
        <span>{{ $subject->created_at->format('M d, Y') }}</span>

        @if ($subject->tags->isNotEmpty())
            <span>
                @foreach ($subject->tags as $tag)
                    <a href="{{ $tag->url }}">{{ $tag->name }}</a>
                @endforeach
            </span>
        @endif
    </div>
</header>
<div class='ck-content'>
    {!! BaseHelper::clean($subject->content) !!}
</div>
<br />
{!! apply_filters(BASE_FILTER_PUBLIC_COMMENT_AREA, null, $subject) !!}

@php $relatedSubjects = get_related_subjects($subject->getKey(), 2); @endphp

@if ($relatedSubjects->isNotEmpty())
    <footer>
        @foreach ($relatedSubjects as $relatedItem)
            <div>
                <article>
                    <div><a href="{{ $relatedItem->url }}"></a>
                        <img
                            src="{{ RvMedia::getImageUrl($relatedItem->image, null, false, RvMedia::getDefaultImage()) }}"
                            alt="{{ $relatedItem->name }}"
                        >
                    </div>
                    <header><a href="{{ $relatedItem->url }}"> {{ $relatedItem->name }}</a></header>
                </article>
            </div>
        @endforeach
    </footer>
@endif
