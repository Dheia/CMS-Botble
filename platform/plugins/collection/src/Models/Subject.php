<?php

namespace Botble\Collection\Models;

use Botble\Base\Casts\SafeContent;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Models\BaseModel;
use Botble\Revision\RevisionableTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Subject extends BaseModel
{
    use RevisionableTrait;

    protected $table = 'subjects';

    protected bool $revisionEnabled = true;

    protected bool $revisionCleanup = true;

    protected int $historyLimit = 20;

    protected array $dontKeepRevisionOf = [
        'content',
        'views',
    ];

    protected $fillable = [
        'name',
        'link',
        'description',
        'content',
        'image',
        'is_featured',
        'format_type',
        'status',
        'author_id',
        'author_type',
    ];

    protected static function booted(): void
    {
        static::deleted(function (Subject $subject) {
            $subject->taxons()->detach();
        });
    }

    protected $casts = [
        'status' => BaseStatusEnum::class,
        'name' => SafeContent::class,
        'link' => SafeContent::class,
        'description' => SafeContent::class,
    ];

    public function taxons(): BelongsToMany
    {
        return $this->belongsToMany(Taxon::class, 'subject_taxons');
    }

    public function author(): MorphTo
    {
        return $this->morphTo()->withDefault();
    }

    protected function firstTaxon(): Attribute
    {
        return Attribute::get(function (): ?Taxon {
            $this->loadMissing('taxons');

            return $this->taxons->first();
        });
    }

    protected function timeReading(): Attribute
    {
        return Attribute::make(
            get: function (): string|null {
                if (! $this->content) {
                    return null;
                }

                $this->loadMissing('metadata');

                $timeToRead = $this->getMetaData('time_to_read', true);

                if ($timeToRead != null) {
                    return number_format((float)$timeToRead);
                }

                return number_format(ceil(str_word_count(strip_tags($this->content)) / 200));
            }
        );
    }
}
