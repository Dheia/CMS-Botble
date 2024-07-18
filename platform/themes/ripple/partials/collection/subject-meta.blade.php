@if ($subject->first_taxon?->name)
    <span class="subject-taxon">
        <a href="{{ $subject->first_taxon->url }}">
        {!! BaseHelper::renderIcon('ti ti-cube') !!} {{ $subject->first_taxon->name }}
        </a>
    </span>
@endif
