<section class="section pt-50 pb-50">
    <div class="container">
        <div class="row">
            @php
                $topSidebarContent = $withSidebar ? dynamic_sidebar('top_sidebar') : null;
            @endphp
            <div @class([
                'col-lg-9' => $topSidebarContent,
                'col-12' => !$topSidebarContent,
            ])>
                <div class="page-content">
                    <div class="subject-group subject-group--single">
                        <div class="subject-group__header">
                            <h3 class="subject-group__title">{{ $title }}</h3>
                        </div>
                        <div class="subject-group__content">
                            <div class="row">
                                @foreach ($subjects as $subject)
                                    @if ($loop->first)
                                        <div class="col-md-6 col-sm-6 col-12">
                                            <article class="subject subject__vertical subject__vertical--single">
                                                <div class="subject__thumbnail">
                                                    {{ RvMedia::image($subject->image, $subject->name, 'medium') }}
                                                    <a
                                                        class="subject__overlay"
                                                        href="{{ $subject->url }}"
                                                        title="{{ $subject->name }}"
                                                    ></a>
                                                </div>
                                                <div class="subject__content-wrap">
                                                    <header class="subject__header">
                                                        <h3 class="subject__title"><a
                                                                href="{{ $subject->url }}"
                                                                title="{{ $subject->name }}"
                                                            >{{ $subject->name }}</a></h3>
                                                        <div class="subject__meta"><span
                                                                class="created__month">{{ $subject->created_at->translatedFormat('M') }}</span><span
                                                                class="created__date"
                                                            >{{ $subject->created_at->translatedFormat('d') }}</span><span
                                                                class="created__year"
                                                            >{{ $subject->created_at->translatedFormat('Y') }}</span>
                                                        </div>
                                                    </header>
                                                    <div class="subject__content">
                                                        <p data-number-line="4">{{ $subject->description }}</p>
                                                    </div>
                                                </div>
                                            </article>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-12">
                                        @else
                                            <article
                                                class="subject subject__horizontal subject__horizontal--single mb-20 clearfix"
                                            >
                                                <div class="subject__thumbnail">
                                                    {{ RvMedia::image($subject->image, $subject->name, 'medium') }}
                                                    <a
                                                        class="subject__overlay"
                                                        href="{{ $subject->url }}"
                                                        title="{{ $subject->name }}"
                                                    ></a>
                                                </div>
                                                <div class="subject__content-wrap">
                                                    <header class="subject__header">
                                                        <h3 class="subject__title"><a
                                                                href="{{ $subject->url }}"
                                                                title="{{ $subject->name }}"
                                                            >{{ $subject->name }}</a></h3>
                                                        <div class="subject__meta"><span
                                                                class="subject__created-at">{{ $subject->created_at->translatedFormat('M d, Y') }}</span>
                                                        </div>
                                                    </header>
                                                </div>
                                            </article>
                                    @endif
                                    @if ($loop->last)
                            </div>
                            @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if ($topSidebarContent)
            <div class="col-lg-3">
                <div class="page-sidebar">
                    {!! $topSidebarContent !!}
                </div>
            </div>
        @endif
    </div>
    </div>
</section>
