<?php

use Botble\Base\Forms\FieldOptions\SelectFieldOption;
use Botble\Base\Forms\FieldOptions\TextFieldOption;
use Botble\Base\Forms\Fields\NumberField;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Forms\Fields\TextField;
use Botble\Base\Models\BaseQueryBuilder;
use Botble\Blog\Models\Category;
use Botble\Collection\Models\Taxon;
use Botble\Shortcode\Compilers\Shortcode as ShortcodeCompiler;
use Botble\Shortcode\Facades\Shortcode;
use Botble\Shortcode\Forms\ShortcodeForm;
use Botble\Theme\Facades\Theme;
use Botble\Theme\Supports\ThemeSupport;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Routing\Events\RouteMatched;
use Illuminate\Support\Arr;

app('events')->listen(RouteMatched::class, function () {
    ThemeSupport::registerGoogleMapsShortcode();
    ThemeSupport::registerYoutubeShortcode();

    if (is_plugin_active('blog')) {
        Shortcode::setPreviewImage('blog-posts', Theme::asset()->url('images/ui-blocks/blog-posts.png'));

        Shortcode::register(
            'featured-posts',
            __('Featured posts'),
            __('Featured posts'),
            function (ShortcodeCompiler $shortcode) {
                $posts = get_featured_posts((int)$shortcode->limit ?: 5, [
                    'author',
                ]);

                return Theme::partial('shortcodes.featured-posts', compact('posts'));
            }
        );

        Shortcode::setAdminConfig('featured-posts', function (array $attributes) {
            return ShortcodeForm::createFromArray($attributes)
                ->add('limit', NumberField::class, TextFieldOption::make()->label(__('Limit'))->toArray());
        });

        Shortcode::setPreviewImage('featured-posts', Theme::asset()->url('images/ui-blocks/featured-posts.png'));

        Shortcode::register(
            'recent-posts',
            __('Recent posts'),
            __('Recent posts'),
            function (ShortcodeCompiler $shortcode) {
                $posts = get_latest_posts(7, [], ['slugable']);

                $withSidebar = ($shortcode->with_sidebar ?: 'yes') === 'yes';

                return Theme::partial('shortcodes.recent-posts', [
                    'title' => $shortcode->title,
                    'withSidebar' => $withSidebar,
                    'posts' => $posts,
                ]);
            }
        );

        Shortcode::setPreviewImage('recent-posts', Theme::asset()->url('images/ui-blocks/recent-posts.png'));

        Shortcode::setAdminConfig('recent-posts', function (array $attributes) {
            return ShortcodeForm::createFromArray($attributes)
                ->add('title', TextField::class, TextFieldOption::make()->label(__('Title'))->toArray())
                ->add(
                    'with_sidebar',
                    SelectField::class,
                    SelectFieldOption::make()
                        ->label(__('With top sidebar?'))
                        ->choices(['yes' => __('Yes'), 'no' => __('No')])
                        ->defaultValue('yes')
                        ->toArray()
                );
        });

        Shortcode::register(
            'featured-categories-posts',
            __('Featured categories posts'),
            __('Featured categories posts'),
            function (ShortcodeCompiler $shortcode) {
                $with = [
                    'slugable',
                    'posts' => function (BelongsToMany|BaseQueryBuilder $query) {
                        $query
                            ->wherePublished()
                            ->orderByDesc('created_at');
                    },
                    'posts.slugable',
                ];

                if (is_plugin_active('language-advanced')) {
                    $with[] = 'posts.translations';
                }

                $posts = collect();

                if ($categoryId = $shortcode->category_id) {
                    $with['posts'] = function (BelongsToMany|BaseQueryBuilder $query) {
                        $query
                            ->wherePublished()
                            ->orderByDesc('created_at')
                            ->take(6);
                    };

                    $category = Category::query()
                        ->with($with)
                        ->wherePublished()
                        ->where('id', $categoryId)
                        ->select([
                            'id',
                            'name',
                            'description',
                            'icon',
                        ])
                        ->first();

                    if ($category) {
                        $posts = $category->posts;
                    } else {
                        $posts = collect();
                    }
                } else {
                    $categories = get_featured_categories(2, $with);

                    foreach ($categories as $category) {
                        $posts = $posts->merge($category->posts->take(3));
                    }

                    $posts = $posts->sortByDesc('created_at');
                }

                $withSidebar = ($shortcode->with_sidebar ?: 'yes') === 'yes';

                return Theme::partial(
                    'shortcodes.featured-categories-posts',
                    [
                        'title' => $shortcode->title,
                        'withSidebar' => $withSidebar,
                        'posts' => $posts,
                    ]
                );
            }
        );

        Shortcode::setPreviewImage(
            'featured-categories-posts',
            Theme::asset()->url('images/ui-blocks/featured-categories-posts.png')
        );

        Shortcode::setAdminConfig('featured-categories-posts', function (array $attributes) {
            $categories = Category::query()
                ->wherePublished()
                ->select('name', 'id')
                ->get()
                ->mapWithKeys(fn ($item) => [$item->id => $item->name])
                ->all();

            return ShortcodeForm::createFromArray($attributes)
                ->add('title', TextField::class, TextFieldOption::make()->label(__('Title'))->toArray())
                ->add(
                    'category_id',
                    SelectField::class,
                    SelectFieldOption::make()
                        ->label(__('Category'))
                        ->choices(['' => __('All')] + $categories)
                        ->selected(Arr::get($attributes, 'category_id'))
                        ->searchable()
                        ->toArray()
                )
                ->add(
                    'with_sidebar',
                    SelectField::class,
                    SelectFieldOption::make()
                        ->label(__('With primary sidebar?'))
                        ->choices(['yes' => __('Yes'), 'no' => __('No')])
                        ->defaultValue('yes')
                        ->toArray()
                );
        });
    }

    if (is_plugin_active('collection')) {
        Shortcode::setPreviewImage('collection-subjects', Theme::asset()->url('images/ui-blocks/collection-subjects.png'));

        Shortcode::register(
            'featured-subjects',
            __('Featured subjects'),
            __('Featured subjects'),
            function (ShortcodeCompiler $shortcode) {
                $subjects = get_featured_subjects((int)$shortcode->limit ?: 5, [
                    'author',
                ]);

                return Theme::partial('shortcodes.featured-subjects', compact('subjects'));
            }
        );

        Shortcode::setAdminConfig('featured-subjects', function (array $attributes) {
            return ShortcodeForm::createFromArray($attributes)
                ->add('limit', NumberField::class, TextFieldOption::make()->label(__('Limit'))->toArray());
        });

        Shortcode::setPreviewImage('featured-subjects', Theme::asset()->url('images/ui-blocks/featured-subjects.png'));

        Shortcode::register(
            'recent-subjects',
            __('Recent subjects'),
            __('Recent subjects'),
            function (ShortcodeCompiler $shortcode) {
                $subjects = get_latest_subjects(7, [], ['slugable']);

                $withSidebar = ($shortcode->with_sidebar ?: 'yes') === 'yes';

                return Theme::partial('shortcodes.recent-subjects', [
                    'title' => $shortcode->title,
                    'withSidebar' => $withSidebar,
                    'subjects' => $subjects,
                ]);
            }
        );

        Shortcode::setPreviewImage('recent-subjects', Theme::asset()->url('images/ui-blocks/recent-subjects.png'));

        Shortcode::setAdminConfig('recent-subjects', function (array $attributes) {
            return ShortcodeForm::createFromArray($attributes)
                ->add('title', TextField::class, TextFieldOption::make()->label(__('Title'))->toArray())
                ->add(
                    'with_sidebar',
                    SelectField::class,
                    SelectFieldOption::make()
                        ->label(__('With top sidebar?'))
                        ->choices(['yes' => __('Yes'), 'no' => __('No')])
                        ->defaultValue('yes')
                        ->toArray()
                );
        });

        Shortcode::register(
            'featured-taxon-subjects',
            __('Featured taxon subjects'),
            __('Featured taxon subjects'),
            function (ShortcodeCompiler $shortcode) {
                $with = [
                    'slugable',
                    'subjects' => function (BelongsToMany|BaseQueryBuilder $query) {
                        $query
                            ->wherePublished()
                            ->orderByDesc('created_at');
                    },
                    'subjects.slugable',
                ];

                if (is_plugin_active('language-advanced')) {
                    $with[] = 'subjects.translations';
                }

                $subjects = collect();

                if ($taxonId = $shortcode->taxon_id) {
                    $with['subjects'] = function (BelongsToMany|BaseQueryBuilder $query) {
                        $query
                            ->wherePublished()
                            ->orderByDesc('created_at')
                            ->take(6);
                    };

                    $taxon = Taxon::query()
                        ->with($with)
                        ->wherePublished()
                        ->where('id', $taxonId)
                        ->select([
                            'id',
                            'name',
                            'description',
                            'icon',
                        ])
                        ->first();

                    if ($taxon) {
                        $subjects = $taxon->subjects;
                    } else {
                        $subjects = collect();
                    }
                } else {
                    $taxons = get_featured_taxon(2, $with);

                    foreach ($taxons as $taxon) {
                        $subjects = $subjects->merge($taxon->subjects->take(3));
                    }

                    $subjects = $subjects->sortByDesc('created_at');
                }

                $withSidebar = ($shortcode->with_sidebar ?: 'yes') === 'yes';

                return Theme::partial(
                    'shortcodes.featured-taxon-subjects',
                    [
                        'title' => $shortcode->title,
                        'withSidebar' => $withSidebar,
                        'subjects' => $subjects,
                    ]
                );
            }
        );

        Shortcode::setPreviewImage(
            'featured-taxon-subjects',
            Theme::asset()->url('images/ui-blocks/featured-taxon-subjects.png')
        );

        Shortcode::setAdminConfig('featured-taxon-subjects', function (array $attributes) {
            $taxon = Taxon::query()
                ->wherePublished()
                ->select('name', 'id')
                ->get()
                ->mapWithKeys(fn ($item) => [$item->id => $item->name])
                ->all();

            return ShortcodeForm::createFromArray($attributes)
                ->add('title', TextField::class, TextFieldOption::make()->label(__('Title'))->toArray())
                ->add(
                    'taxon_id',
                    SelectField::class,
                    SelectFieldOption::make()
                        ->label(__('Subject Taxon'))
                        ->choices(['' => __('All')] + $taxon)
                        ->selected(Arr::get($attributes, 'taxon_id'))
                        ->searchable()
                        ->toArray()
                )
                ->add(
                    'with_sidebar',
                    SelectField::class,
                    SelectFieldOption::make()
                        ->label(__('With primary sidebar?'))
                        ->choices(['yes' => __('Yes'), 'no' => __('No')])
                        ->defaultValue('yes')
                        ->toArray()
                );
        });
    }

    if (is_plugin_active('contact')) {
        Shortcode::setPreviewImage('contact-form', Theme::asset()->url('images/ui-blocks/contact-form.png'));
    }

    if (is_plugin_active('gallery')) {
        Shortcode::setPreviewImage('gallery', Theme::asset()->url('images/ui-blocks/gallery.png'));

        Shortcode::register(
            'all-galleries',
            __('All galleries'),
            __('All galleries'),
            function (ShortcodeCompiler $shortcode) {
                return Theme::partial('shortcodes.all-galleries', ['limit' => (int)$shortcode->limit]);
            }
        );

        Shortcode::setPreviewImage('all-galleries', Theme::asset()->url('images/ui-blocks/all-galleries.png'));

        Shortcode::setAdminConfig('all-galleries', function (array $attributes) {
            return ShortcodeForm::createFromArray($attributes)
                ->add('limit', NumberField::class, TextFieldOption::make()->label(__('Limit'))->toArray());
        });
    }
});
