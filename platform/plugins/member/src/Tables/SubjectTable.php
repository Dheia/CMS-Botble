<?php

namespace Botble\Member\Tables;

use Botble\Base\Models\BaseQueryBuilder;
use Botble\Collection\Models\Category;
use Botble\Collection\Models\Subject;
use Botble\Member\Models\Member;
use Botble\Member\Tables\Traits\ForMember;
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
use Botble\Table\Columns\ImageColumn;
use Botble\Table\Columns\NameColumn;
use Botble\Table\Columns\StatusColumn;
use Botble\Table\HeaderActions\CreateHeaderAction;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Relations\Relation as EloquentRelation;
use Illuminate\Database\Query\Builder as QueryBuilder;

class SubjectTable extends TableAbstract
{
    use ForMember;

    public function setup(): void
    {
        $this
            ->model(Subject::class)
            ->addHeaderAction(CreateHeaderAction::make()->url(route('public.member.subjects.create')))
            ->addColumns([
                CreatedAtColumn::make(),
                ImageColumn::make(),
                NameColumn::make()->route('public.member.subjects.edit'),
                FormattedColumn::make('categories_name')
                    ->title(trans('plugins/collection::subjects.categories'))
                    ->width(150)
                    ->orderable(false)
                    ->searchable(false)
                    ->getValueUsing(function (FormattedColumn $column) {
                        return implode(', ', $column->getItem()->categories->pluck('name')->all());
                    }),
                CreatedAtColumn::make(),
                StatusColumn::make(),
            ])
            ->addActions([
                EditAction::make()->route('public.member.subjects.edit'),
                DeleteAction::make()->route('public.member.subjects.destroy'),
            ])
            ->addBulkActions([
                DeleteBulkAction::make()
                    ->beforeDispatch(function (Subject $model, array $ids) {
                        foreach ($ids as $id) {
                            $subject = Subject::query()->findOrFail($id);

                            if (auth('member')->id() !== $subject->author_id) {
                                abort(403);
                            }
                        }
                    }),
            ])
            ->queryUsing(function (EloquentBuilder $query) {
                return $query
                    ->with(['categories'])
                    ->select([
                        'id',
                        'name',
                        'image',
                        'created_at',
                        'status',
                        'updated_at',
                    ])
                    ->where([
                        'author_id' => auth('member')->id(),
                        'author_type' => Member::class,
                    ]);
            })
            ->onFilterQuery(
                function (
                    EloquentBuilder|QueryBuilder|EloquentRelation $query,
                    string $key,
                    string $operator,
                    string|null $value
                ) {
                    if (! $value || $key !== 'category') {
                        return false;
                    }

                    return $query->whereHas(
                        'categories',
                        fn (BaseQueryBuilder $query) => $query->where('categories.id', $value)
                    );
                }
            );
    }

    public function getFilters(): array
    {
        return [
            NameBulkChange::make(),
            StatusBulkChange::make(),
            CreatedAtBulkChange::make(),
            SelectBulkChange::make()
                ->name('category')
                ->title(trans('plugins/collection::subjects.category'))
                ->searchable()
                ->choices(fn () => Category::query()->pluck('name', 'id')->all()),
        ];
    }
}
