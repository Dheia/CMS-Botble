@if (is_plugin_active('collection') && $subjects->isNotEmpty())
    @if ($sidebar == 'footer_sidebar')
        <div class="col-lg-3 col-md-3 col-sm-6 col-12">
            <div class="widget widget--transparent widget__footer">
            @else
                <div class="widget widget__recent-subject">
    @endif
    @if ($config['name'])
        <div class="widget__header">
            <h3 class="widget__title">{{ $config['name'] }}</h3>
        </div>
    @endif
    <div class="widget__content">
        <ul @if ($sidebar == 'footer_sidebar') class="list list--light list--fadeIn" @endif>
            @foreach ($subjects as $subject)
                <li>
                    @if ($sidebar == 'footer_sidebar')
                        <a
                            href="{{ $subject->url }}"
                            title="{{ $subject->name }}"
                            data-number-line="2"
                        >{{ $subject->name }}</a>
                    @else
                        <article class="subject subject__widget clearfix">
                            <div class="subject__thumbnail">
                                {{ RvMedia::image($subject->image, $subject->name, 'thumb') }}
                                <a
                                    href="{{ $subject->url }}"
                                    title="{{ $subject->name }}"
                                    class="subject__overlay"
                                ></a>
                            </div>
                            <header class="subject__header">
                                <h4 class="subject__title text-truncate-2"><a
                                        href="{{ $subject->url }}"
                                        title="{{ $subject->name }}"
                                        data-number-line="2"
                                    >{{ $subject->name }}</a></h4>
                                <div class="subject__meta"><span
                                        class="subject__created-at">{{ $subject->created_at->translatedFormat('M d, Y') }}</span>
                                </div>
                            </header>
                        </article>
                    @endif
                </li>
            @endforeach
        </ul>
    </div>
    </div>
    @if ($sidebar == 'footer_sidebar')
        </div>
    @endif
@endif
