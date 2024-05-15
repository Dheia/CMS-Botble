<?php

namespace Botble\Collection\Services;

use Botble\Collection\Models\Subject;
use Botble\Collection\Services\Abstracts\StoreCategoryServiceAbstract;
use Illuminate\Http\Request;

class StoreCategoryService extends StoreCategoryServiceAbstract
{
    public function execute(Request $request, Subject $subject): void
    {
        $subject->categories()->sync($request->input('categories', []));
    }
}
