<?php

namespace Botble\Collection\Tables;

use Botble\ACL\Models\User;
use Botble\Base\Facades\Html;
use Botble\Base\Models\BaseQueryBuilder;
use Botble\Collection\Exports\SubjectExport;
use Botble\Collection\Models\Taxon;
use Botble\Collection\Models\Subject;
use Botble\Table\Abstracts\TableAbstract;
use Botble\Table\Actions\DeleteAction;
use Botble\Table\Actions\EditAction;
use Botble\Table\BulkActions\DeleteBulkAction;
use Botble\Table\BulkChanges\CreatedAtBulkChange;
use Botble\Table\BulkChanges\NameBulkChange;
use Botble\Table\BulkChanges\SelectBulkChange;
use Botble\Table\BulkChanges\StatusBulkChange;
use Botble\Table\Columns\CreatedAtColumn;
use Botble\Table\Columns\FormattedColumn;
use Botble\Table\Columns\IdColumn;
use Botble\Table\Columns\ImageColumn;
use Botble\Table\Columns\NameColumn;
use Botble\Table\Columns\StatusColumn;
use Botble\Table\HeaderActions\CreateHeaderAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\Relation as EloquentRelation;
use Illuminate\Database\Query\Builder as QueryBuilder;

class SubjectTable extends TableAbstract
{
    protected string $exportClass = SubjectExport::class;

    protected int $defaultSortColumn = 6;

    public function setup(): void
    {
        $this
            ->model(Subject::class)
            ->addHeaderAction(CreateHeaderAction::make()->route('subjects.create'))
            ->addActions([
                EditAction::make()->route('subjects.edit'),
                DeleteAction::make()->route('subjects.destroy'),
            ])
            ->addColumns([
                IdColumn::make(),
                ImageColumn::make(),
                NameColumn::make()->route('subjects.edit'),
                FormattedColumn::make('taxons_name')
                    ->title(trans('plugins/collection::subjects.taxons'))
                    ->width(150)
                    ->orderable(false)
                    ->searchable(false)
                    ->getValueUsing(function (FormattedColumn $column) {
                        $taxons = $column
                            ->getItem()
                            ->taxons
                            ->sortBy('name')
                            ->map(function (Taxon $taxon) {
                                return Html::link(route('taxons.edit', $taxon->getKey()), $taxon->name, ['target' => '_blank']);
                            })
                            ->all();

                        return implode(', ', $taxons);
                    })
                    ->withEmptyState(),
                FormattedColumn::make('author_id')
                    ->title(trans('plugins/collection::subjects.author'))
                    ->width(150)
                    ->orderable(false)
                    ->searchable(false)
                    ->getValueUsing(fn (FormattedColumn $column) => $column->getItem()->author?->name)
                    ->renderUsing(function (FormattedColumn $column) {
                        $subject = $column->getItem();
                        $author = $subject->author;

                        if (! $author->getKey()) {
                            return null;
                        }

                        if ($subject->author_id && $subject->author_type === User::class) {
                            return Html::link($author->url, $author->name, ['target' => '_blank']);
                        }

                        return null;
                    })
                    ->withEmptyState(),
                CreatedAtColumn::make(),
                StatusColumn::make(),
            ])
            ->addBulkActions([
                DeleteBulkAction::make()->permission('subjects.destroy'),
            ])
            ->addBulkChanges([
                NameBulkChange::make(),
                StatusBulkChange::make(),
                CreatedAtBulkChange::make(),
                SelectBulkChange::make()
                    ->name('taxon')
                    ->title(trans('plugins/collection::subjects.taxon'))
                    ->searchable()
                    ->choices(fn () => Taxon::query()->pluck('name', 'id')->all()),
            ])
            ->queryUsing(function (Builder $query) {
                return $query
                    ->with([
                        'taxons' => function (BelongsToMany $query) {
                            $query->select(['taxons.id', 'taxons.name']);
                        },
                        'author',
                    ])
                    ->select([
                        'id',
                        'name',
                        'image',
                        'created_at',
                        'status',
                        'updated_at',
                        'author_id',
                        'author_type',
                    ]);
            })
            ->onAjax(function (SubjectTable $table) {
                return $table->toJson(
                    $table
                        ->table
                        ->eloquent($table->query())
                        ->filter(function ($query) {
                            if ($keyword = $this->request->input('search.value')) {
                                $keyword = '%' . $keyword . '%';

                                return $query
                                    ->where('name', 'LIKE', $keyword)
                                    ->orWhereHas('taxons', function ($subQuery) use ($keyword) {
                                        return $subQuery
                                            ->where('name', 'LIKE', $keyword);
                                    })
                                    ->orWhereHas('author', function ($subQuery) use ($keyword) {
                                        return $subQuery
                                            ->where('first_name', 'LIKE', $keyword)
                                            ->orWhere('last_name', 'LIKE', $keyword)
                                            ->orWhereRaw('concat(first_name, " ", last_name) LIKE ?', $keyword);
                                    });
                            }

                            return $query;
                        })
                );
            })
            ->onFilterQuery(
                function (
                    EloquentBuilder|QueryBuilder|EloquentRelation $query,
                    string $key,
                    string $operator,
                    string|null $value
                ) {
                    if (! $value || $key !== 'taxon') {
                        return false;
                    }

                    return $query->whereHas(
                        'taxons',
                        fn (BaseQueryBuilder $query) => $query->where('taxons.id', $value)
                    );
                }
            )
            ->onSavingBulkChangeItem(function (Subject $item, string $inputKey, string|null $inputValue) {
                if ($inputKey !== 'taxon') {
                    return null;
                }

                $item->taxons()->sync([$inputValue]);

                return $item;
            });
    }
}
