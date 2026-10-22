<?php

namespace App\Http\Controllers;

use App\Models\CaseStudy;
use Illuminate\Contracts\View\View;

class CaseStudyController extends Controller
{
    public function index(): View
    {
        $caseStudies = CaseStudy::query()->published()->with('service')->get();

        // The page is linked only once there is at least one published story.
        abort_if($caseStudies->isEmpty(), 404);

        return view('impact.index', ['caseStudies' => $caseStudies]);
    }

    public function show(CaseStudy $caseStudy): View
    {
        abort_unless($caseStudy->is_active, 404);

        return view('impact.show', [
            'caseStudy' => $caseStudy->load('service'),
            'others' => CaseStudy::query()->published()->whereKeyNot($caseStudy->getKey())->limit(2)->get(),
        ]);
    }
}
