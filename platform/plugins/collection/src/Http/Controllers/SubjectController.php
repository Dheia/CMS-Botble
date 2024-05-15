<?php

namespace Botble\Collection\Http\Controllers;

use Botble\ACL\Models\User;
use Botble\Base\Http\Actions\DeleteResourceAction;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Base\Supports\Breadcrumb;
use Botble\Collection\Forms\SubjectForm;
use Botble\Collection\Http\Requests\SubjectRequest;
use Botble\Collection\Models\Subject;
use Botble\Collection\Services\StoreTaxonService;
use Botble\Collection\Tables\SubjectTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubjectController extends BaseController
{
    protected function breadcrumb(): Breadcrumb
    {
        return parent::breadcrumb()
            ->add(trans('plugins/collection::base.menu_name'))
            ->add(trans('plugins/collection::subjects.menu_name'), route('subjects.index'));
    }

    public function index(SubjectTable $dataTable)
    {
        $this->pageTitle(trans('plugins/collection::subjects.menu_name'));

        return $dataTable->renderTable();
    }

    public function create()
    {
        $this->pageTitle(trans('plugins/collection::subjects.create'));

        return SubjectForm::create()->renderForm();
    }

    public function store(
        SubjectRequest $request,
        StoreTaxonService $taxonService
    ) {
        $subjectForm = SubjectForm::create();

        $subjectForm->saving(function (SubjectForm $form) use ($request, $taxonService) {
            $form
                ->getModel()
                ->fill([
                    ...$request->input(),
                    'author_id' => Auth::guard()->id(),
                    'author_type' => User::class,
                ])
                ->save();

            $subject = $form->getModel();

            $form->fireModelEvents($subject);

            $taxonService->execute($request, $subject);
        });

        return $this
            ->httpResponse()
            ->setPreviousRoute('subjects.index')
            ->setNextRoute('subjects.edit', $subjectForm->getModel()->getKey())
            ->withCreatedSuccessMessage();
    }

    public function edit(Subject $subject)
    {
        $this->pageTitle(trans('core/base::forms.edit_item', ['name' => $subject->name]));

        return SubjectForm::createFromModel($subject)->renderForm();
    }

    public function update(
        Subject $subject,
        SubjectRequest $request,
        StoreTaxonService $taxonService,
    ) {
        SubjectForm::createFromModel($subject)
            ->setRequest($request)
            ->saving(function (SubjectForm $form) use ($taxonService) {
                $request = $form->getRequest();

                $subject = $form->getModel();
                $subject->fill($request->input());
                $subject->save();

                $form->fireModelEvents($subject);

                $taxonService->execute($request, $subject);
            });

        return $this
            ->httpResponse()
            ->setPreviousRoute('subjects.index')
            ->withUpdatedSuccessMessage();
    }

    public function destroy(Subject $subject)
    {
        return DeleteResourceAction::make($subject);
    }

    public function getWidgetRecentSubjects(Request $request): BaseHttpResponse
    {
        $limit = $request->integer('paginate', 10);
        $limit = $limit > 0 ? $limit : 10;

        $subjects = Subject::query()
            ->with(['slugable'])
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();

        return $this
            ->httpResponse()
            ->setData(view('plugins/collection::widgets.subjects', compact('subjects', 'limit'))->render());
    }
}
