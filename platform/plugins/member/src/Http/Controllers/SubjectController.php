<?php

namespace Botble\Member\Http\Controllers;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Facades\EmailHandler;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Collection\Models\Subject;
use Botble\Collection\Models\Tag;
use Botble\Collection\Services\StoreCategoryService;
use Botble\Collection\Services\StoreTagService;
use Botble\Media\Facades\RvMedia;
use Botble\Member\Forms\SubjectForm;
use Botble\Member\Http\Requests\SubjectRequest;
use Botble\Member\Models\Member;
use Botble\Member\Models\MemberActivityLog;
use Botble\Member\Tables\SubjectTable;
use Illuminate\Http\Request;

class SubjectController extends BaseController
{
    public function index(SubjectTable $subjectTable)
    {
        $this->pageTitle(trans('plugins/collection::subjects.subjects'));

        return $subjectTable->renderTable();
    }

    public function create()
    {
        $this->pageTitle(trans('plugins/member::member.write_a_subject'));

        return SubjectForm::create()->renderForm();
    }

    public function store(SubjectRequest $request, StoreTagService $tagService, StoreCategoryService $categoryService)
    {
        $this->processRequestData($request);

        $subjectForm = SubjectForm::create();
        $subjectForm
            ->saving(function (SubjectForm $form) use ($categoryService, $tagService, $request) {
                $subject = $form->getModel();
                $subject
                    ->fill([...$request->except('status'),
                        'author_id' => auth('member')->id(),
                        'author_type' => Member::class,
                        'status' => BaseStatusEnum::PENDING,
                    ])
                    ->save();

                MemberActivityLog::query()->create([
                    'action' => 'create_subject',
                    'reference_name' => $subject->name,
                    'reference_url' => route('public.member.subjects.edit', $subject->getKey()),
                ]);

                $tagService->execute($request, $subject);

                $categoryService->execute($request, $subject);

                EmailHandler::setModule(MEMBER_MODULE_SCREEN_NAME)
                    ->setVariableValues([
                        'subject_name' => $subject->name,
                        'subject_url' => route('subjects.edit', $subject->getKey()),
                        'subject_author' => $subject->author->name,
                    ])
                    ->sendUsingTemplate('new-pending-subject');
            });

        return $this
            ->httpResponse()
            ->setPreviousRoute('public.member.subjects.index')
            ->setNextRoute('public.member.subjects.edit', $subjectForm->getModel()->getKey())
            ->withCreatedSuccessMessage();
    }

    public function edit(Subject $subject)
    {
        /**
         * @var Subject $subject
         */
        $subject = Subject::query()
            ->where([
                'id' => $subject->getKey(),
                'author_id' => auth('member')->id(),
                'author_type' => Member::class,
            ])
            ->firstOrFail();

        $this->pageTitle(trans('core/base::forms.edit_item', ['name' => $subject->name]));

        return SubjectForm::createFromModel($subject)->renderForm();
    }

    public function update(Subject $subject, SubjectRequest $request, StoreTagService $tagService, StoreCategoryService $categoryService)
    {
        /**
         * @var Subject $subject
         */
        $subject = Subject::query()
            ->where([
                'id' => $subject->getKey(),
                'author_id' => auth('member')->id(),
                'author_type' => Member::class,
            ])
            ->firstOrFail();

        $this->processRequestData($request);

        $subjectForm = SubjectForm::createFromModel($subject);

        $subjectForm
            ->saving(function (SubjectForm $form) use ($categoryService, $tagService, $request) {
                $subject = $form->getModel();

                $subject
                    ->fill($request->except('status'))
                    ->save();

                MemberActivityLog::query()->create([
                    'action' => 'update_subject',
                    'reference_name' => $subject->name,
                    'reference_url' => route('public.member.subjects.edit', $subject->getKey()),
                ]);

                $tagService->execute($request, $subject);

                $categoryService->execute($request, $subject);
            });

        return $this
            ->httpResponse()
            ->setPreviousRoute('public.member.subjects.index')
            ->withUpdatedSuccessMessage();
    }

    protected function processRequestData(Request $request): Request
    {
        $account = auth('member')->user();

        if ($request->hasFile('image_input')) {
            $result = RvMedia::handleUpload($request->file('image_input'), 0, $account->upload_folder);
            if (! $result['error']) {
                $file = $result['data'];
                $request->merge(['image' => $file->url]);
            }
        }

        $shortcodeCompiler = shortcode()->getCompiler();

        $request->merge([
            'content' => $shortcodeCompiler->strip(
                $request->input('content'),
                $shortcodeCompiler->whitelistShortcodes()
            ),
        ]);

        $except = [
            'status',
            'is_featured',
        ];

        foreach ($except as $item) {
            $request->request->remove($item);
        }

        return $request;
    }

    public function destroy(Subject $subject)
    {
        $subject = Subject::query()
            ->where([
                'id' => $subject->getKey(),
                'author_id' => auth('member')->id(),
                'author_type' => Member::class,
            ])
            ->firstOrFail();

        $subject->delete();

        MemberActivityLog::query()->create([
            'action' => 'delete_subject',
            'reference_name' => $subject->name,
        ]);

        return $this
            ->httpResponse()
            ->withDeletedSuccessMessage();
    }

    public function getAllTags()
    {
        return Tag::query()->pluck('name')->all();
    }
}
