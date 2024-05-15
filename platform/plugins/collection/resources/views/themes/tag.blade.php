<div>
    <h3>{{ $tag->name }}</h3>
    {!! Theme::breadcrumb()->render() !!}
</div>

<div>
    @if ($subjects->isNotEmpty())
        @foreach ($subjects as $subject)
            <article>
                <div>
                    <a href="{{ $subject->url }}"><img
                            src="{{ RvMedia::getImageUrl($subject->image, null, false, RvMedia::getDefaultImage()) }}"
                            alt="{{ $subject->name }}"
                        ></a>
                </div>
                <div>
                    <header>
                        <h3><a href="{{ $subject->url }}">{{ $subject->name }}</a></h3>
                        <div>
                            {{ $subject->created_at->format('M d, Y') }} - <span>{{ $subject->author->name }}</span>>
                            @if ($subject->taxon->first())
                                <a
                                    href="{{ $subject->taxon->first()->url }}">{{ $subject->taxon->first()->name }}</a>
                            @endif
                        </div>
                    </header>
                    <div>
                        <p>{{ $subject->description }}</p>
                    </div>
                </div>
            </article>
        @endforeach
        <div>
            {!! $subjects->links() !!}
        </div>
    @else
        <div>
            <p>{{ __('There is no data to display!') }}</p>
        </div>
    @endif
</div>
