<?php

namespace Botble\Member\Listeners;

use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Facades\BaseHelper;
use Botble\Collection\Models\Subject;
use Botble\Member\Models\Member;
use Botble\Member\Models\MemberActivityLog;
use Exception;

class UpdatedCollectionListener
{
    public function handle(UpdatedContentEvent $event): void
    {
        try {
            $subject = $event->data;

            if (! $subject instanceof Subject) {
                return;
            }

            if ($subject->getKey() &&
                $subject->author_type === Member::class &&
                auth('member')->check() &&
                $subject->author_id == auth('member')->id()
            ) {
                MemberActivityLog::query()->create([
                    'action' => 'your_subject_updated_by_admin',
                    'reference_name' => $subject->name,
                    'reference_url' => route('public.member.subjects.edit', $subject->getKey()),
                ]);
            }
        } catch (Exception $exception) {
            BaseHelper::logError($exception);
        }
    }
}
