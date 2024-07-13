@if ($subject->first_taxon?->name)
    <span class="subject-taxon">
        {!! BaseHelper::renderIcon('ti ti-cube') !!}
        <a href="{{ $subject->first_taxon->url }}">{{ $subject->first_taxon->name }}</a>
    </span>
@endif

<!-- <span class="created_at">
    {!! BaseHelper::renderIcon('ti ti-clock') !!} {{ $subject->created_at->translatedFormat('M d Y') }}
</span>

@if ($subject->author->name)
    <span class="subject-author">{!! BaseHelper::renderIcon('ti ti-user-circle') !!} <span>{{ $subject->author->name }}</span></span>
@endif -->
