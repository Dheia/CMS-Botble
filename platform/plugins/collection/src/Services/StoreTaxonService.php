<?php

namespace Botble\Collection\Services;

use Botble\Collection\Models\Subject;
use Botble\Collection\Services\Abstracts\StoreTaxonServiceAbstract;
use Illuminate\Http\Request;

class StoreTaxonService extends StoreTaxonServiceAbstract
{
    public function execute(Request $request, Subject $subject): void
    {
        $subject->taxons()->sync($request->input('taxons', []));
    }
}
