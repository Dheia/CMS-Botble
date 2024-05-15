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
                    <div><span>{{ $subject->created_at->format('M d, Y') }}</span><span>{{ $subject->author->name }}</span> -
                        {{ __('Taon') }}:
                        @foreach ($subject->taxon as $taxon)
                            <a href="{{ $taxon->url }}">{{ $taxon->name }}</a>
                            @if (!$loop->last)
                                ,
                            @endif
                        @endforeach
                    </div>
                </header>
                <div>
                    <p>{{ $subject->description }}</p>
                </div>
            </div>
        </article>
    @endforeach
    <div>
        {!! $subjects->withQueryString()->links() !!}
    </div>
@endif
