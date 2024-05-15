<?php

namespace Botble\Collection\Database\Traits;

use Botble\ACL\Models\User;
use Botble\Collection\Models\Category;
use Botble\Collection\Models\Subject;
use Botble\Collection\Models\Tag;
use Botble\Slug\Facades\SlugHelper;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

trait HasCollectionSeeder
{
    protected function createCollectionCategories(array $categories, bool $truncate = true): void
    {
        if ($truncate) {
            Category::query()->truncate();
        }

        $faker = $this->fake();

        foreach ($categories as $index => $item) {
            $item['description'] ??= $faker->text();
            $item['is_featured'] ??= ! isset($item['parent_id']) && $index != 0;
            $item['author_id'] ??= 1;
            $item['parent_id'] ??= 0;

            $category = $this->createCollectionCategory(Arr::except($item, 'children'));

            if (Arr::has($item, 'children')) {
                foreach (Arr::get($item, 'children', []) as $child) {
                    $child['parent_id'] = $category->getKey();

                    $this->createCollectionCategory($child);
                }
            }

            $this->createMetadata($category, $item);
        }
    }

    protected function createCollectionTags(array $tags, bool $truncate = true): void
    {
        if ($truncate) {
            Tag::query()->truncate();
        }

        foreach ($tags as $item) {
            /**
             * @var Tag $tag
             */
            $tag = Tag::query()->create(Arr::except($item, ['metadata']));

            SlugHelper::createSlug($tag);

            $this->createMetadata($tag, $item);
        }
    }

    protected function createCollectionSubjects(array $subjects, bool $truncate = true): void
    {
        if ($truncate) {
            Subject::query()->truncate();
            DB::table('subject_categories')->truncate();
            DB::table('subject_tags')->truncate();
        }

        $faker = $this->fake();

        $categoryIds = Category::query()->pluck('id');
        $tagIds = Tag::query()->pluck('id');
        $userIds = User::query()->pluck('id');

        foreach ($subjects as $item) {
            $item['views'] ??= $faker->numberBetween(100, 2500);
            $item['description'] ??= $faker->realText();
            $item['is_featured'] ??= $faker->boolean();
            $item['content'] ??= $this->removeBaseUrlFromString((string) $item['content']);
            $item['author_id'] ??= $userIds->random();
            $item['author_type'] ??= User::class;

            /**
             * @var Subject $subject
             */
            $subject = Subject::query()->create(Arr::except($item, ['metadata']));

            $subject->categories()->sync(array_unique([
                $categoryIds->random(),
                $categoryIds->random(),
            ]));

            $subject->tags()->sync(array_unique([
                $tagIds->random(),
                $tagIds->random(),
                $tagIds->random(),
            ]));

            SlugHelper::createSlug($subject);

            $this->createMetadata($subject, $item);
        }
    }

    protected function getCategoryId(string $name): int|string
    {
        return Category::query()->where('name', $name)->value('id');
    }

    protected function createCollectionCategory(array $item): Category
    {
        /**
         * @var Category $category
         */
        $category = Category::query()->create(Arr::except($item, ['metadata']));

        SlugHelper::createSlug($category);

        $this->createMetadata($category, $item);

        return $category;
    }
}
