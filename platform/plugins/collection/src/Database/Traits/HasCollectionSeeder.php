<?php

namespace Botble\Collection\Database\Traits;

use Botble\ACL\Models\User;
use Botble\Collection\Models\Taxon;
use Botble\Collection\Models\Subject;
use Botble\Slug\Facades\SlugHelper;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

trait HasCollectionSeeder
{
    protected function createCollectionTaxons(array $taxons, bool $truncate = true): void
    {
        if ($truncate) {
            Taxon::query()->truncate();
        }

        $faker = $this->fake();

        foreach ($taxons as $index => $item) {
            $item['description'] ??= $faker->text();
            $item['is_featured'] ??= ! isset($item['parent_id']) && $index != 0;
            $item['author_id'] ??= 1;
            $item['parent_id'] ??= 0;

            $taxon = $this->createCollectionTaxon(Arr::except($item, 'children'));

            if (Arr::has($item, 'children')) {
                foreach (Arr::get($item, 'children', []) as $child) {
                    $child['parent_id'] = $taxon->getKey();

                    $this->createCollectionTaxon($child);
                }
            }

            $this->createMetadata($taxon, $item);
        }
    }

    protected function createCollectionSubjects(array $subjects, bool $truncate = true): void
    {
        if ($truncate) {
            Subject::query()->truncate();
            DB::table('subject_taxons')->truncate();
        }

        $faker = $this->fake();

        $taxonIds = Taxon::query()->pluck('id');
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

            $subject->taxons()->sync(array_unique([
                $taxonIds->random(),
                $taxonIds->random(),
            ]));

            SlugHelper::createSlug($subject);

            $this->createMetadata($subject, $item);
        }
    }

    protected function getTaxonId(string $name): int|string
    {
        return Taxon::query()->where('name', $name)->value('id');
    }

    protected function createCollectionTaxon(array $item): Taxon
    {
        /**
         * @var Taxon $taxon
         */
        $taxon = Taxon::query()->create(Arr::except($item, ['metadata']));

        SlugHelper::createSlug($taxon);

        $this->createMetadata($taxon, $item);

        return $taxon;
    }
}
