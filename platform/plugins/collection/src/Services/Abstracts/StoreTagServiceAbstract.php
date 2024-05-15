<?php

namespace Botble\Collection\Services\Abstracts;

use Botble\Collection\Models\Subject;
use Illuminate\Http\Request;

abstract class StoreTagServiceAbstract
{
    abstract public function execute(Request $request, Subject $subject): void;
}
