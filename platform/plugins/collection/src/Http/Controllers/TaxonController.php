<?php

namespace Botble\Collection\Http\Controllers;

use Botble\ACL\Models\User;
use Botble\Base\Facades\Assets;
use Botble\Base\Forms\FormAbstract;
use Botble\Base\Http\Actions\DeleteResourceAction;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Requests\UpdateTreeCategoryRequest;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Base\Supports\Breadcrumb;
use Botble\Base\Supports\RepositoryHelper;
use Botble\Collection\Forms\TaxonForm;
use Botble\Collection\Http\Requests\TaxonRequest;
use Botble\Collection\Models\Taxon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaxonController extends BaseController
{
    protected function breadcrumb(): Breadcrumb
    {
        return parent::breadcrumb()
            ->add(trans('plugins/collection::base.menu_name'))
            ->add(trans('plugins/collection::taxon.menu'), route('taxon.index'));
    }

    public function index(Request $request)
    {
        $this->pageTitle(trans('plugins/collection::taxon.menu'));

        $taxon = Taxon::query()
            ->orderByDesc('is_default')
            ->orderBy('order')
            ->orderBy('created_at')
            ->with('slugable')
            ->withCount('subjects');

        $taxon = RepositoryHelper::applyBeforeExecuteQuery($taxon, new Taxon())->get();

        if ($request->ajax()) {
            $data = view('core/base::forms.partials.tree-taxon', $this->getOptions(compact('taxon')))
                ->render();

            return $this
                ->httpResponse()
                ->setData($data);
        }

        Assets::addStylesDirectly('vendor/core/core/base/css/tree-category.css')
            ->addScriptsDirectly('vendor/core/core/base/js/tree-category.js');

        $form = TaxonForm::create(['template' => 'core/base::forms.form-tree-category']);
        $form = $this->setFormOptions($form, null, compact('taxon'));

        return $form->renderForm();
    }

    public function create(Request $request)
    {
        $this->pageTitle(trans('plugins/collection::taxon.create'));

        if ($request->ajax()) {
            return $this
                ->httpResponse()
                ->setData($this->getForm());
        }

        return TaxonForm::create()->renderForm();
    }

    public function store(TaxonRequest $request)
    {
        if ($request->input('is_default')) {
            Taxon::query()->where('id', '>', 0)->update(['is_default' => 0]);
        }

        $form = TaxonForm::create();
        $form
            ->saving(function (TaxonForm $form) use ($request) {
                $form
                    ->getModel()
                    ->fill([...$request->validated(),
                        'author_id' => Auth::guard()->id(),
                        'author_type' => User::class,
                    ])
                    ->save();
            });

        $response = $this->httpResponse();

        $taxon = $form->getModel();

        if ($request->ajax()) {
            if ($response->isSaving()) {
                $form = $this->getForm();
            } else {
                $form = $this->getForm($taxon);
            }

            $response->setData([
                'model' => $taxon,
                'form' => $form,
            ]);
        }

        return $response
            ->setPreviousRoute('taxon.index')
            ->setNextRoute('taxon.edit', $taxon->getKey())
            ->withCreatedSuccessMessage();
    }

    public function edit(Taxon $taxon, Request $request)
    {
        if ($request->ajax()) {
            return $this
                ->httpResponse()
                ->setData($this->getForm($taxon));
        }

        $this->pageTitle(trans('core/base::forms.edit_item', ['name' => $taxon->name]));

        return TaxonForm::createFromModel($taxon)->renderForm();
    }

    public function update(Taxon $taxon, TaxonRequest $request)
    {
        if ($request->input('is_default')) {
            Taxon::query()->where('id', '!=', $taxon->getKey())->update(['is_default' => 0]);
        }

        TaxonForm::createFromModel($taxon)->save();

        $response = $this->httpResponse();

        if ($request->ajax()) {
            if ($response->isSaving()) {
                $form = $this->getForm();
            } else {
                $form = $this->getForm($taxon);
            }

            $response->setData([
                'model' => $taxon,
                'form' => $form,
            ]);
        }

        return $response
            ->setPreviousRoute('taxon.index')
            ->withUpdatedSuccessMessage();
    }

    public function destroy(Taxon $taxon)
    {
        return DeleteResourceAction::make($taxon);
    }

    public function updateTree(UpdateTreeCategoryRequest $request): BaseHttpResponse
    {
        Taxon::updateTree($request->validated('data'));

        return $this
            ->httpResponse()
            ->withUpdatedSuccessMessage();
    }

    protected function getForm(Taxon|null $model = null): string
    {
        $options = ['template' => 'core/base::forms.form-no-wrap'];

        if ($model) {
            $options['model'] = $model;
        }

        $form = TaxonForm::create($options);

        $form = $this->setFormOptions($form, $model);

        return $form->renderForm();
    }

    protected function setFormOptions(FormAbstract $form, ?Taxon $model = null, array $options = []): FormAbstract
    {
        if (! $model) {
            $form->setUrl(route('taxon.create'));
        }

        if (! Auth::guard()->user()->hasPermission('taxon.create') && ! $model) {
            $class = $form->getFormOption('class');
            $form->setFormOption('class', $class . ' d-none');
        }

        $form->setFormOptions($this->getOptions($options));

        return $form;
    }

    protected function getOptions(array $options = []): array
    {
        return array_merge([
            'canCreate' => Auth::guard()->user()->hasPermission('taxon.create'),
            'canEdit' => Auth::guard()->user()->hasPermission('taxon.edit'),
            'canDelete' => Auth::guard()->user()->hasPermission('taxon.destroy'),
            'createRoute' => 'taxon.create',
            'editRoute' => 'taxon.edit',
            'deleteRoute' => 'taxon.destroy',
            'updateTreeRoute' => 'taxon.update-tree',
        ], $options);
    }
}
