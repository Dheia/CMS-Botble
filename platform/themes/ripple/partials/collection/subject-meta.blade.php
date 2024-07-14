@if ($subject->first_taxon?->name)
    <span class="subject-taxon">
        {!! BaseHelper::renderIcon('ti ti-cube') !!}
        <a href="{{ $subject->first_taxon->url }}">{{ $subject->first_taxon->name }}</a>
    </span>
@endif
