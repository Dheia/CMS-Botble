<?php

namespace Botble\Collection\Providers;

use Botble\Api\Facades\ApiHelper;
use Botble\Base\Facades\DashboardMenu;
use Botble\Base\Facades\PanelSectionManager;
use Botble\Base\PanelSections\PanelSectionItem;
use Botble\Base\Supports\ServiceProvider;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Botble\Collection\Models\Category;
use Botble\Collection\Models\Subject;
use Botble\Collection\Models\Tag;
use Botble\Collection\Repositories\Eloquent\CategoryRepository;
use Botble\Collection\Repositories\Eloquent\SubjectRepository;
use Botble\Collection\Repositories\Eloquent\TagRepository;
use Botble\Collection\Repositories\Interfaces\CategoryInterface;
use Botble\Collection\Repositories\Interfaces\SubjectInterface;
use Botble\Collection\Repositories\Interfaces\TagInterface;
use Botble\Language\Facades\Language;
use Botble\LanguageAdvanced\Supports\LanguageAdvancedManager;
use Botble\SeoHelper\Facades\SeoHelper;
use Botble\Setting\PanelSections\SettingOthersPanelSection;
use Botble\Shortcode\View\View;
use Botble\Slug\Facades\SlugHelper;
use Botble\Theme\Events\ThemeRoutingBeforeEvent;
use Botble\Theme\Facades\SiteMapManager;

/**
 * @since 02/07/2016 09:50 AM
 */
class CollectionServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register(): void
    {
        $this->app->bind(SubjectInterface::class, function () {
            return new SubjectRepository(new Subject());
        });

        $this->app->bind(CategoryInterface::class, function () {
            return new CategoryRepository(new Category());
        });

        $this->app->bind(TagInterface::class, function () {
            return new TagRepository(new Tag());
        });
    }

    public function boot(): void
    {
        SlugHelper::registerModule(Subject::class, 'Collection Subjects');
        SlugHelper::registerModule(Category::class, 'Collection Categories');
        SlugHelper::registerModule(Tag::class, 'Collection Tags');

        SlugHelper::setPrefix(Tag::class, 'tag', true);
        SlugHelper::setPrefix(Subject::class, null, true);
        SlugHelper::setPrefix(Category::class, null, true);

        $this
            ->setNamespace('plugins/collection')
            ->loadHelpers()
            ->loadAndPublishConfigurations(['permissions', 'general'])
            ->loadAndPublishViews()
            ->loadAndPublishTranslations()
            ->loadRoutes()
            ->loadMigrations()
            ->publishAssets();

        if (class_exists('ApiHelper') && ApiHelper::enabled()) {
            $this->loadRoutes(['api']);
        }

        $this->app->register(EventServiceProvider::class);

        $this->app['events']->listen(ThemeRoutingBeforeEvent::class, function () {
            SiteMapManager::registerKey([
                'collection-categories',
                'collection-tags',
                'collection-subjects-((?:19|20|21|22)\d{2})-(0?[1-9]|1[012])',
            ]);
        });

        DashboardMenu::default()->beforeRetrieving(function () {
            DashboardMenu::make()
                ->registerItem([
                    'id' => 'cms-plugins-collection',
                    'priority' => 3,
                    'name' => 'plugins/collection::base.menu_name',
                    'icon' => 'ti ti-article',
                ])
                ->registerItem([
                    'id' => 'cms-plugins-collection-subject',
                    'priority' => 1,
                    'parent_id' => 'cms-plugins-collection',
                    'name' => 'plugins/collection::subjects.menu_name',
                    'route' => 'subjects.index',
                ])
                ->registerItem([
                    'id' => 'cms-plugins-collection-categories',
                    'priority' => 2,
                    'parent_id' => 'cms-plugins-collection',
                    'name' => 'plugins/collection::categories.menu_name',
                    'route' => 'categories.index',
                ])
                ->registerItem([
                    'id' => 'cms-plugins-collection-tags',
                    'priority' => 3,
                    'parent_id' => 'cms-plugins-collection',
                    'name' => 'plugins/collection::tags.menu_name',
                    'route' => 'tags.index',
                ]);
        });

        PanelSectionManager::default()->beforeRendering(function () {
            PanelSectionManager::registerItem(
                SettingOthersPanelSection::class,
                fn () => PanelSectionItem::make('collection')
                    ->setTitle(trans('plugins/collection::base.settings.title'))
                    ->withIcon('ti ti-file-settings')
                    ->withDescription(trans('plugins/collection::base.settings.description'))
                    ->withPriority(120)
                    ->withRoute('collection.settings')
            );
        });

        if (defined('LANGUAGE_MODULE_SCREEN_NAME')) {
            if (
                defined('LANGUAGE_ADVANCED_MODULE_SCREEN_NAME') &&
                $this->app['config']->get('plugins.collection.general.use_language_v2')
            ) {
                LanguageAdvancedManager::registerModule(Subject::class, [
                    'name',
                    'description',
                    'content',
                ]);

                LanguageAdvancedManager::registerModule(Category::class, [
                    'name',
                    'description',
                ]);

                LanguageAdvancedManager::registerModule(Tag::class, [
                    'name',
                    'description',
                ]);
            } else {
                Language::registerModule([Subject::class, Category::class, Tag::class]);
            }
        }

        $this->app->booted(function () {
            SeoHelper::registerModule([Subject::class, Category::class, Tag::class]);

            $configKey = 'packages.revision.general.supported';
            config()->set($configKey, array_merge(config($configKey, []), [Subject::class]));

            $this->app->register(HookServiceProvider::class);
        });

        if (function_exists('shortcode')) {
            view()->composer([
                'plugins/collection::themes.subject',
                'plugins/collection::themes.category',
                'plugins/collection::themes.tag',
            ], function (View $view) {
                $view->withShortcodes();
            });
        }
    }
}
