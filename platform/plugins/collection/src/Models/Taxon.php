<?php

namespace Botble\Collection\Models;

use Botble\Base\Casts\SafeContent;
use Botble\Base\Contracts\HasTreeTaxon as HasTreeTaxonContract;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Facades\Html;
use Botble\Base\Models\BaseModel;
use Botble\Base\Traits\HasTreeTaxon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;

class Taxon extends BaseModel implements HasTreeTaxonContract
{
    use HasTreeTaxon;

    protected $table = 'taxons';

    protected $fillable = [
        'name',
        'description',
        'parent_id',
        'icon',
        'is_featured',
        'order',
        'is_default',
        'status',
        'author_id',
        'author_type',
    ];

    protected $casts = [
        'status' => BaseStatusEnum::class,
        'name' => SafeContent::class,
        'description' => SafeContent::class,
        'is_default' => 'bool',
        'order' => 'int',
    ];

    protected static function booted(): void
    {
        static::deleted(function (Taxon $taxon) {
            $taxon->children()->each(fn (Taxon $child) => $child->delete());

            $taxon->subjects()->detach();
        });
    }

    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'subject_taxons')->with('slugable');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Taxon::class, 'parent_id')->withDefault();
    }

    public function children(): HasMany
    {
        return $this->hasMany(Taxon::class, 'parent_id');
    }

    public function activeChildren(): HasMany
    {
        return $this
            ->children()
            ->wherePublished()
            ->with(['slugable', 'activeChildren']);
    }

    protected function parents(): Attribute
    {
        return Attribute::get(function (): Collection {
            $parents = collect();

            $parent = $this->parent;

            while ($parent->id) {
                $parents->push($parent);
                $parent = $parent->parent;
            }

            return $parents;
        });
    }

    protected function badgeWithCount(): Attribute
    {
        return Attribute::get(function (): HtmlString {
            return Html::tag('span', sprintf('(%s)', $this->subjects_count), [
                'data-bs-toggle' => 'tooltip',
                'data-bs-original-title' => trans('plugins/collection::taxons.total_subjects', ['total' => $this->subjects_count]),
            ]);
        });
    }
}
