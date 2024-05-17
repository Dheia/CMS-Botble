<?php

namespace Database\Seeders;

use Botble\Base\Supports\BaseSeeder;
use Botble\Blog\Database\Traits\HasBlogSeeder;
use Botble\Blog\Models\Category;
use Botble\Collection\Database\Traits\HasCollectionSeeder;
use Botble\Collection\Models\Taxon;
use Botble\Menu\Database\Traits\HasMenuSeeder;
use Botble\Page\Database\Traits\HasPageSeeder;
use Botble\Page\Models\Page;

class MenuSeeder extends BaseSeeder
{
    use HasMenuSeeder;
    use HasPageSeeder;
    use HasBlogSeeder;
    use HasCollectionSeeder;

    public function run(): void
    {
        $categories = [];
        $taxons = [];

        foreach (Category::query()->limit(5)->get() as $category) {
            $categories[] = [
                'title' => $category->name,
                'reference_id' => $category->id,
                'reference_type' => Category::class,
            ];
        }

        foreach (Taxon::query()->limit(5)->get() as $taxon) {
            $taxons[] = [
                'title' => $taxon->name,
                'reference_id' => $taxon->id,
                'reference_type' => Taxon::class,
            ];
        }

        $data = [
            [
                'name' => 'Main menu',
                'slug' => 'main-menu',
                'location' => 'main-menu',
                'items' => [
                    [
                        'title' => 'Home',
                        'url' => '/',
                    ],
                    [
                        'title' => 'Blog',
                        'reference_id' => $this->getPageId('Blog'),
                        'reference_type' => Page::class,
                    ],
                    [
                        'title' => 'Collection',
                        'reference_id' => $this->getPageId('Collection'),
                        'reference_type' => Page::class,
                    ],
                    [
                        'title' => 'Galleries',
                        'reference_id' => $this->getPageId('Galleries'),
                        'reference_type' => Page::class,
                    ],
                    [
                        'title' => 'Contact',
                        'reference_id' => $this->getPageId('Contact'),
                        'reference_type' => Page::class,
                    ],
                ],
            ],

            [
                'name' => 'Featured Categories',
                'slug' => 'featured-categories',
                'items' => $categories,
            ],

            [
                'name' => 'Featured Taxon',
                'slug' => 'featured-taxon',
                'items' => $taxons,
            ],

            [
                'name' => 'Social',
                'slug' => 'social',
                'items' => [
                    [
                        'title' => 'Facebook',
                        'url' => 'https://facebook.com',
                        'icon_font' => 'ti ti-brand-facebook',
                        'target' => '_blank',
                    ],
                    [
                        'title' => 'Twitter',
                        'url' => 'https://twitter.com',
                        'icon_font' => 'ti ti-brand-x',
                        'target' => '_blank',
                    ],
                    [
                        'title' => 'GitHub',
                        'url' => 'https://github.com',
                        'icon_font' => 'ti ti-brand-github',
                        'target' => '_blank',
                    ],

                    [
                        'title' => 'Linkedin',
                        'url' => 'https://linkedin.com',
                        'icon_font' => 'ti ti-brand-linkedin',
                        'target' => '_blank',
                    ],
                ],
            ],
        ];

        $this->createMenus($data);
    }
}
