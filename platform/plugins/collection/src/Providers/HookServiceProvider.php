<?php

namespace Botble\Collection\Providers;

use Botble\Base\Facades\Assets;
use Botble\Base\Facades\BaseHelper;
use Botble\Base\Facades\Html;
use Botble\Base\Forms\FieldOptions\SelectFieldOption;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Supports\ServiceProvider;
use Botble\Collection\Models\Taxon;
use Botble\Collection\Models\Subject;
use Botble\Collection\Services\CollectionService;
use Botble\Dashboard\Events\RenderingDashboardWidgets;
use Botble\Dashboard\Supports\DashboardWidgetInstance;
use Botble\Media\Facades\RvMedia;
use Botble\Menu\Events\RenderingMenuOptions;
use Botble\Menu\Facades\Menu;
use Botble\Page\Models\Page;
use Botble\Page\Tables\PageTable;
use Botble\Shortcode\Compilers\Shortcode;
use Botble\Shortcode\Facades\Shortcode as ShortcodeFacade;
use Botble\Shortcode\Forms\ShortcodeForm;
use Botble\Slug\Models\Slug;
use Botble\Theme\Events\RenderingAdminBar;
use Botble\Theme\Events\RenderingThemeOptionSettings;
use Botble\Theme\Facades\AdminBar;
use Botble\Theme\Facades\Theme;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class HookServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Menu::addMenuOptionModel(Taxon::class);

        $this->app['events']->listen(RenderingMenuOptions::class, function () {
            add_action(MENU_ACTION_SIDEBAR_OPTIONS, [$this, 'registerMenuOptions'], 2);
        });

        $this->app['events']->listen(RenderingDashboardWidgets::class, function () {
            add_filter(DASHBOARD_FILTER_ADMIN_LIST, [$this, 'registerDashboardWidgets'], 21, 2);
        });

        add_filter(BASE_FILTER_PUBLIC_SINGLE_DATA, [$this, 'handleSingleView'], 2);

        if (defined('PAGE_MODULE_SCREEN_NAME')) {
            add_filter(PAGE_FILTER_FRONT_PAGE_CONTENT, [$this, 'renderCollectionPage'], 2, 2);
        }

        PageTable::beforeRendering(function () {
            add_filter(PAGE_FILTER_PAGE_NAME_IN_ADMIN_LIST, [$this, 'addAdditionNameToPageName'], 147, 2);
        });

        $this->app['events']->listen(RenderingAdminBar::class, function () {
            AdminBar::registerLink(
                trans('plugins/collection::subjects.subject'),
                route('subjects.create'),
                'add-new',
                'subjects.create'
            );
        });

        if (function_exists('add_shortcode')) {
            shortcode()
                ->register(
                    $shortcodeName = 'collection-subjects',
                    trans('plugins/collection::base.short_code_name'),
                    trans('plugins/collection::base.short_code_description'),
                    [$this, 'renderCollectionSubjects']
                )
                ->setAdminConfig(
                    $shortcodeName,
                    function (array $attributes) {
                        $taxons = Taxon::query()
                            ->wherePublished()
                            ->pluck('name', 'id')
                            ->all();

                        return ShortcodeForm::createFromArray($attributes)
                            ->add('paginate', 'number', [
                                'label' => trans('plugins/collection::base.number_subjects_per_page'),
                                'attr' => [
                                    'placeholder' => trans('plugins/collection::base.number_subjects_per_page'),
                                ],
                            ])
                            ->add(
                                'taxon_ids[]',
                                SelectField::class,
                                SelectFieldOption::make()
                                    ->label(__('Select taxons'))
                                    ->choices($taxons)
                                    ->when(Arr::get($attributes, 'taxon_ids'), function (SelectFieldOption $option, $taxonsIds) {
                                        $option->selected(explode(',', $taxonsIds));
                                    })
                                    ->multiple()
                                    ->searchable()
                                    ->helperText(__('Leave taxons empty if you want to show subjects from all taxons.'))
                                    ->toArray()
                            );
                    }
                );
        }

        $this->app['events']->listen(RenderingThemeOptionSettings::class, function () {
            add_action(RENDERING_THEME_OPTIONS_PAGE, [$this, 'addThemeOptions'], 35);
        });

        if (defined('THEME_FRONT_HEADER') && setting('collection_subject_schema_enabled', 1)) {
            add_action(BASE_ACTION_PUBLIC_RENDER_SINGLE, function ($screen, $subject) {
                add_filter(THEME_FRONT_HEADER, function ($html) use ($subject) {
                    if (! $subject instanceof Subject) {
                        return $html;
                    }

                    $schemaType = setting('collection_subject_schema_type', 'NewsArticle');

                    if (! in_array($schemaType, ['NewsArticle', 'News', 'Article', 'CollectionSubjecting'])) {
                        $schemaType = 'NewsArticle';
                    }

                    $schema = [
                        '@context' => 'https://schema.org',
                        '@type' => $schemaType,
                        'mainEntityOfPage' => [
                            '@type' => 'WebPage',
                            '@id' => $subject->url,
                        ],
                        'headline' => BaseHelper::clean($subject->name),
                        'description' => BaseHelper::clean($subject->description),
                        'image' => [
                            '@type' => 'ImageObject',
                            'url' => RvMedia::getImageUrl($subject->image, null, false, RvMedia::getDefaultImage()),
                        ],
                        'author' => [
                            '@type' => 'Person',
                            'url' => fn () => BaseHelper::getHomepageUrl(),
                            'name' => class_exists($subject->author_type) ? $subject->author->name : '',
                        ],
                        'publisher' => [
                            '@type' => 'Organization',
                            'name' => theme_option('site_title'),
                            'logo' => [
                                '@type' => 'ImageObject',
                                'url' => RvMedia::getImageUrl(theme_option('logo')),
                            ],
                        ],
                        'datePublished' => $subject->created_at->toDateString(),
                        'dateModified' => $subject->updated_at->toDateString(),
                    ];

                    return $html . Html::tag('script', json_encode($schema), ['type' => 'application/ld+json'])
                            ->toHtml();
                }, 35);
            }, 35, 2);
        }
    }

    public function addThemeOptions(): void
    {
        $pages = Page::query()
            ->wherePublished()
            ->pluck('name', 'id')
            ->all();

        theme_option()
            ->setSection([
                'title' => trans('plugins/collection::base.settings.title'),
                'id' => 'opt-text-subsection-collection',
                'subsection' => true,
                'icon' => 'ti ti-edit',
                'fields' => [
                    [
                        'id' => 'collection_page_id',
                        'type' => 'customSelect',
                        'label' => trans('plugins/collection::base.collection_page_id'),
                        'attributes' => [
                            'name' => 'collection_page_id',
                            'list' => [0 => trans('plugins/collection::base.select')] + $pages,
                            'value' => '',
                            'options' => [
                                'class' => 'form-control',
                            ],
                        ],
                    ],
                    [
                        'id' => 'number_of_subjects_in_a_taxon',
                        'type' => 'number',
                        'label' => trans('plugins/collection::base.number_subjects_per_page_in_taxon'),
                        'attributes' => [
                            'name' => 'number_of_subjects_in_a_taxon',
                            'value' => 12,
                            'options' => [
                                'class' => 'form-control',
                            ],
                        ],
                    ],
                ],
            ]);
    }

    public function registerMenuOptions(): void
    {
        if (Auth::guard()->user()->hasPermission('taxons.index')) {
            Menu::registerMenuOptions(Taxon::class, trans('plugins/collection::taxons.menu'));
        }
    }

    public function registerDashboardWidgets(array $widgets, Collection $widgetSettings): array
    {
        if (! Auth::guard()->user()->hasPermission('subjects.index')) {
            return $widgets;
        }

        Assets::addScriptsDirectly(['/vendor/core/plugins/collection/js/collection.js']);

        return (new DashboardWidgetInstance())
            ->setPermission('subjects.index')
            ->setKey('widget_subjects_recent')
            ->setTitle(trans('plugins/collection::subjects.widget_subjects_recent'))
            ->setIcon('fas fa-edit')
            ->setColor('yellow')
            ->setRoute(route('subjects.widget.recent-subjects'))
            ->setBodyClass('')
            ->setColumn('col-md-6 col-sm-6')
            ->init($widgets, $widgetSettings);
    }

    public function handleSingleView(Slug|array $slug): Slug|array
    {
        return (new CollectionService())->handleFrontRoutes($slug);
    }

    public function renderCollectionSubjects(Shortcode $shortcode): array|string
    {
        $taxonIds = ShortcodeFacade::fields()->getIds('taxon_ids', $shortcode);

        $subjects = Subject::query()
            ->wherePublished()
            ->orderByDesc('created_at')
            ->with('slugable')
            ->when(! empty($taxonIds), function ($query) use ($taxonIds) {
                $query->whereHas('taxons', function ($query) use ($taxonIds) {
                    $query->whereIn('taxons.id', $taxonIds);
                });
            })
            ->paginate((int)$shortcode->paginate ?: 12);

        $view = 'plugins/collection::themes.templates.subjects';
        $themeView = Theme::getThemeNamespace() . '::views.templates.subjects';

        if (view()->exists($themeView)) {
            $view = $themeView;
        }

        return view($view, compact('subjects'))->render();
    }

    public function renderCollectionPage(string|null $content, Page $page): string|null
    {
        if ($page->getKey() == $this->getCollectionPageId()) {
            $view = 'plugins/collection::themes.subject_loop';

            if (view()->exists($viewPath = Theme::getThemeNamespace() . '::views.subject_loop')) {
                $view = $viewPath;
            }

            return view($view, [
                'subjects' => get_all_subjects(true, (int)theme_option('number_of_subjects_in_a_taxon', 12)),
            ])->render();
        }

        return $content;
    }

    public function addAdditionNameToPageName(string|null $name, Page $page): string|null
    {
        if ($page->getKey() == $this->getCollectionPageId()) {
            $subTitle = Html::tag('span', trans('plugins/collection::base.collection_page'), ['class' => 'additional-page-name'])
                ->toHtml();

            if (Str::contains($name, ' —')) {
                return $name . ', ' . $subTitle;
            }

            return $name . ' —' . $subTitle;
        }

        return $name;
    }

    protected function getCollectionPageId(): int|string|null
    {
        return theme_option('collection_page_id', setting('collection_page_id'));
    }
}
