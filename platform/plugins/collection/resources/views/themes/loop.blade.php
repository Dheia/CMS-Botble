@foreach ($subjects as $subject)
    <div>
        <article>
            <div><a href="{{ $subject->url }}"></a>
                <img
                    src="{{ RvMedia::getImageUrl($subject->image, null, false, RvMedia::getDefaultImage()) }}"
                    alt="{{ $subject->name }}"
                >
            </div>
            <header><a href="{{ $subject->url }}"> {{ $subject->name }}</a></header>
        </article>
    </div>
@endforeach

<div class="pagination">
    {!! $subjects->withQueryString()->links() !!}
</div>
