<?php

namespace Botble\Collection\Http\Controllers;

use Botble\Base\Facades\BaseHelper;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Collection\Repositories\Interfaces\SubjectInterface;
use Botble\SeoHelper\Facades\SeoHelper;
use Botble\Theme\Facades\Theme;
use Illuminate\Http\Request;

class PublicController extends BaseController
{
    public function getSearch(Request $request, SubjectInterface $subjectRepository)
    {
        $query = BaseHelper::stringify($request->input('q'));

        if (! $query) {
            abort(404);
        }

        $title = __('Search result for: ":query"', compact('query'));

        SeoHelper::setTitle($title)
            ->setDescription($title);

        $subjects = $subjectRepository->getSearch($query, 0, (int)theme_option('number_of_subjects_in_a_taxon', 12));
        
        $posts = $subjects;

        Theme::breadcrumb()->add($title, route('public.subject_search'));

        return Theme::scope('search', compact('posts'))
            ->render();
    }
}
