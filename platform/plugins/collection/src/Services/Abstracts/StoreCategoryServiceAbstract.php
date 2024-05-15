<?php

namespace Botble\Collection\Services\Abstracts;

use Botble\Collection\Models\Subject;
use Illuminate\Http\Request;

abstract class StoreCategoryServiceAbstract
{
    public function __construct()
    {
    }

    abstract public function execute(Request $request, Subject $subject): void;
}
