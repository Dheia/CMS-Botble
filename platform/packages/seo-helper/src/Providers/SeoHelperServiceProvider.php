<?php

namespace Botble\SeoHelper\Providers;

use Botble\Base\Supports\ServiceProvider;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Botble\SeoHelper\Contracts\SeoHelperContract;
use Botble\SeoHelper\Contracts\SeoMetaContract;
use Botble\SeoHelper\Contracts\SeoOpenGraphContract;
use Botble\SeoHelper\Contracts\SeoTwitterContract;
use Botble\SeoHelper\SeoHelper;
use Botble\SeoHelper\SeoMeta;
use Botble\SeoHelper\SeoOpenGraph;
use Botble\SeoHelper\SeoTwitter;

/**
 * SeoHelperServiceProvider
 */
class SeoHelperServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register(): void
    {
        $this->app->bind(SeoMetaContract::class, SeoMeta::class);
        $this->app->bind(SeoHelperContract::class, SeoHelper::class);
        $this->app->bind(SeoOpenGraphContract::class, SeoOpenGraph::class);
        $this->app->bind(SeoTwitterContract::class, SeoTwitter::class);
    }

    public function boot(): void
    {
        $this
            ->setNamespace('packages/seo-helper')
            ->loadHelpers()
            ->loadAndPublishConfigurations(['general'])
            ->loadAndPublishViews()
            ->loadAndPublishTranslations()
            ->publishAssets();

        $this->app->register(EventServiceProvider::class);
        $this->app->register(HookServiceProvider::class);
    }
}
