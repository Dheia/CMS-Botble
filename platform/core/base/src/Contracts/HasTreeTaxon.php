<?php

namespace Botble\Base\Contracts;

interface HasTreeTaxon
{
    public static function updateTree(array $data): void;
}
